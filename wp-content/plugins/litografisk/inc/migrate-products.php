<?php

function create_product_attribute_artist_term($term_name, $slug = '') {
    $taxonomy = 'pa_kunstnere';
    // Check if the term already exists.
    $existing = term_exists($term_name, $taxonomy);
    if ($existing) {
        return 'exists';
    }

    // Insert the new term.
    $result = wp_insert_term(
        $term_name,
        $taxonomy,
        [
            'slug' => $slug ?: sanitize_title($term_name),
        ]
    );

    if (is_wp_error($result)) {
        return $result;
    }

    return 'created';
}

/**
 * Copy terms from the `artist` taxonomy to the WooCommerce product attribute taxonomy `pa_kunstnere`.
 *
 * @return array{
 *     migrated_terms:int,
 *     created_terms:int,
 *     skipped_terms:int
 * }
 */
function litografisk_migrate_artist_to_kunstnere_attribute() {
	$source_taxonomy = 'artist';
	$target_taxonomy = 'pa_kunstnere';

	if ( ! taxonomy_exists( $source_taxonomy ) ) {
		return array(
			'migrated_terms' => 0,
			'created_terms'  => 0,
			'skipped_terms'  => 0,
		);
	}

	if ( ! taxonomy_exists( $target_taxonomy ) ) {
		return array(
			'migrated_terms' => 0,
			'created_terms'  => 0,
			'skipped_terms'  => 0,
		);
	}


    $terms = get_terms(
        array(
            'taxonomy'   => $source_taxonomy,
            'hide_empty' => false,
        )
    );

    $created_terms = 0;

    foreach ($terms as $term) {
        $result = create_product_attribute_artist_term($term->name, $term->slug);
        if ($result === 'created') {
            $created_terms++;
        }
    }

    return [
        'migrated_terms' => count($terms),
        'created_terms'  => $created_terms,
        'skipped_terms'  => count($terms) - $created_terms,
    ];
}

/**
 * Extract price from a line that starts with "Pris" and ends with "kr" (case-insensitive).
 *
 * Examples:
 * - Pris 4000 kr
 * - Pris 4.000 KR
 * - Pris: 4,500 kr
 * - Pris    12.500 Kr
 *
 * @param string $text
 * @return int|float|null
 */

function extract_price($text){
    // Remove lasted period or comma at the end of the line.
    $text = preg_replace('/[.,]$/', '', $text);

    if (preg_match('/^\s*Pris\s*:?\s*([\d\.,]+)\s*(?:kr\.?)?\s*$/im', $text, $matches)) {

        $price = $matches[1];

        $price = preg_replace('/[.,](?=\d{3}\b)/', '', $price);
        // Return float if the value contains decimals, otherwise int.

        return strpos($price, '.') !== false ? (float) $price : (int) $price;
    }

    return null;
}

function litografisk_find_product_by_source_item_id($source_item_id) {
    $args = array(
        'post_type'  => 'product',
        'meta_query' => array(
            array(
                'key'   => '_source_item_id',
                'value' => $source_item_id,
            ),
        ),
        'posts_per_page' => 1,
    );

    $query = new WP_Query($args);
    return $query->have_posts() ? $query->posts[0] : null;
}

function litografisk_migrate_products() {


	$products = get_posts(
		array(
			'post_type'      => 'item',
			'post_status'    => 'any',
			'posts_per_page' => -1
		)
	);

	$created_terms     = 0;
	$migrated_products = 0;
	$skipped_products  = 0;


	foreach ( $products as $productPost ) {
		$product_id = $productPost->ID;
		$artist_terms = wp_get_object_terms( $product_id, 'artist' );
        $feature_image_id = get_post_thumbnail_id( $product_id );
        $price = extract_price($productPost->post_content);
        if (is_null($price)) {
            var_dump($productPost->post_content);
        }

		// Check if a product with the same source item ID already exists.
        $existing_product = litografisk_find_product_by_source_item_id($product_id);
        if ($existing_product) {
            $product = wc_get_product($existing_product->ID);
        } else {
            $product = new WC_Product();
        }

        $product->set_name( $productPost->post_title );
        $product->set_description( $productPost->post_content );
        $product->set_status( $productPost->post_status );
        $product->set_price( $price );
        $product->set_regular_price( $price );
        $product->set_image_id( $feature_image_id );
        $product->set_meta_data( '_source_item_id', $product_id );
        $product->save();

		if ( $product ) {

            $target_term_ids = array();
            foreach ( $artist_terms as $artist_term ) {
                $target_term = get_term_by( 'slug', $artist_term->slug, 'pa_kunstnere' );
                if ( ! $target_term ) {
                    $result = create_product_attribute_artist_term( $artist_term->name, $artist_term->slug );
                    if ( $result === 'created' ) {
                        $created_terms++;
                    }
                    $target_term = get_term_by( 'slug', $artist_term->slug, 'pa_kunstnere' );
                }
                if ( $target_term ) {
                    $target_term_ids[] = (int) $target_term->term_id;
                }
            }


			$attributes = $product->get_attributes();

            $attribute_artsits = 'pa_kunstnere';

			if ( empty( $attributes[ $attribute_artsits ] ) ) {
				$attribute = new WC_Product_Attribute();
				$attribute->set_id( wc_attribute_taxonomy_id_by_name( $attribute_artsits ) );
				$attribute->set_name( $attribute_artsits );
				$attribute->set_options( $target_term_ids );
				$attribute->set_position( count( $attributes ) );
				$attribute->set_visible( true );
				$attribute->set_variation( false );

				$attributes[ $attribute_artsits ] = $attribute;
			} else {
				$existing_options               = $attributes[ $attribute_artsits ]->get_options();
				$attributes[ $attribute_artsits ]->set_options(
					array_values( array_unique( array_map( 'intval', array_merge( $existing_options, $target_term_ids ) ) ) )
				);
			}

			$product->set_attributes( $attributes );
			$product->save();
		}

		++$migrated_products;
	}

	return array(
		'migrated_products' => $migrated_products,
		'created_terms'     => $created_terms,
	);
}

/**
 * Register the Litografisk admin page.
 */
function litografisk_register_admin_menu() {
	add_menu_page(
		'Litografisk',
		'Litografisk',
		'manage_woocommerce',
		'litografisk',
		'litografisk_render_admin_page',
		'dashicons-update'
	);
}
add_action( 'admin_menu', 'litografisk_register_admin_menu' );

/**
 * Render the Litografisk admin page.
 */
function litografisk_render_admin_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Litografisk Migration</h1>


		<?php if ( ! empty( $_POST['action'] ) && $_POST['action'] === 'litografisk_run_migration' ) : ?>
            <?php 
                $result = litografisk_migrate_artist_to_kunstnere_attribute();
            ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						esc_html__( 'Migration completed. Migrated terms: %1$d. Created terms: %2$d. Skipped terms: %3$d.', 'litografisk' ),
						isset( $result['migrated_terms'] ) ? (int) $result['migrated_terms'] : 0,
						isset( $result['created_terms'] ) ? (int) $result['created_terms'] : 0,
						isset( $result['skipped_terms'] ) ? (int) $result['skipped_terms'] : 0
					);
					?>
				</p>
			</div>
		<?php endif; ?>


        <?php if ( ! empty( $_POST['action'] ) && $_POST['action'] === 'litografisk_run_migration_product' ) : ?>
            <?php 
                $result = litografisk_migrate_products();
            ?>
        <?php endif; ?>


		<p>Copy terms from taxonomy <code>artist</code> to WooCommerce attribute <code>pa_kunstnere</code>.</p>

		<form method="post">
			<input type="hidden" name="action" value="litografisk_run_migration">
			<?php wp_nonce_field( 'litografisk_run_migration' ); ?>
			<?php submit_button( 'Run Migration' ); ?>
		</form>
		<form method="post">
			<input type="hidden" name="action" value="litografisk_run_migration_product">
			<?php wp_nonce_field( 'litografisk_run_migration_product' ); ?>
			<?php submit_button( 'Run Migration product' ); ?>
		</form>
	</div>
	<?php
}
