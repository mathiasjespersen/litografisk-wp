<?php if (is_tax('artist')) : ?> 
	<div id="overlay" class="form black">
		<div class="form-wrapper">
			<form action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" method="post" id="order-form">
				<p><?php the_field('shop_intro', 2); ?></p>
				
				<span id="item-info"></span>
				<?php // Inject specific data from item into hidden input field ?>
				<input type="hidden" name="item" value="" id="item">
			
				<div>
					<label for="email">*Email</label>
					<input type="email" name="email" id="email" placeholder="mail@mail.dk" required autocorrect="off" autocapitalize="off" autocomplete="email">
				</div>

				<div>
					<label for="fullname">*Navn</label>
					<input type="text" name="fullname" id="fullname" placeholder="Navn Efternavn" required autocorrect="off" autocomplete="name">
				</div>

				<div>
					<label for="address">*Adresse</label>
					<textarea name="address" id="address" rows="3" placeholder="Gadenavn, Nummer, Sal &#10;Postnummer, By &#10;Land" required autocorrect="off"></textarea>
				</div>	
				
				<div>
					<label for="cvr">CVR (kun virksomheder)</label>
					<input type="text" inputmode="numeric" name="cvr" id="cvr" placeholder="12345678" autocorrect="off">
				</div>
				
				<input type="hidden" name="action" value="contact_form">
				<input type="submit" value="Køb" class="button">
			
			</form>
			<div class="response"><?php the_field('shop_confirm', 2); ?></div>
			<a href="#" class="close"></a>
		</div>
	</div>
<?php endif; ?>

<script type="text/javascript" src="<?php bloginfo( 'template_url' ); ?>/js/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="<?php bloginfo( 'template_url' ); ?>/js/script.js?v=1.03"></script>

<?php wp_footer(); ?>

</body>
</html>