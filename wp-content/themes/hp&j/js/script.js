'use strict';

$(document).ready(function() {

	/**
	 * Settings
	 *
	 */
	var showLink = true, 
		removeTitleDelay = 3000, // initial delay on load
		showGuides = false,
		imageMaxHeight = 580,
		isMobile;

	/**
	 * Globals
	 *
	 */
	var body = $('body'),
		title = $('#title'),
		vw = $(window).width();

		if (vw > 600) {
			isMobile = false;
		} else {
			isMobile = true;
			//showLink = false;
		}


	/**
	 * Functions
	 *
	 */

	// Object-fit fix for IE
	// if ( ! Modernizr.objectfit ) {
	// 	$('.thumbnail').each(function () {
	// 		var $container = $(this),
	// 		imgUrl = $container.find('img').prop('src');
	// 		if (imgUrl) {
	// 			$container
	// 				.css('backgroundImage', 'url(' + imgUrl + ')')
	// 				.addClass('compat-object-fit');
	// 		}  
	// 	});
	// }


	// Set the height of image wrappers to match the baseline grid
	// The images are then displaced with CSS
	function setImageHeightToGrid() {
		$('figure img').each(function() {
			var image = $(this).height(),
				imageHeight = image - (image % 20);
			if (image > imageMaxHeight && !$(this).parents('article').hasClass('item') && !$(this).parents('.module').hasClass('span6')) {
				$(this).addClass('too-high ready').css({'height' : imageMaxHeight + 'px'});
			} else {
				$(this).addClass('ready').parents('figure').css({'height' : imageHeight + 'px'});
			}
		});		
	}

	// Replace preloaded lowres images with higher resolution image
	function setImageScrAfterLoad() {
		$('img.preload').each(function(){
			$(this).attr('src', $(this).data('src'));
		});
	}

	// Show/hide baseline
/*
	if (showGuides) {
		body.addClass('show-baseline-grid');
	}
	$(document).keydown(function(e) {
		if (e.which == 71) {
			body.toggleClass('show-baseline-grid');
		}
	});
*/

	// Toggle visibility of overlay logo/link 
	if (showLink == true) {
		// Show items after load and delay
		var titleSection = $('section').data('title');
		title.text(titleSection);

		// var titleSection = $('section').data('title'),
		// 	titleSection = $('<span class="nowrap">' + titleSection + '</span>');;
		// title.empty().html(titleSection);

		window.setTimeout(function(){
			title.empty();
		}, removeTitleDelay);		

		// Show link in overlay
		$('.menu-item a, a.title').on('mouseenter', function() {
			var link = $(this),
				linkTitle;
			
			if (link.data('title')) {
				linkTitle = link.data('title');
			} else {
				linkTitle = link.text();
			}
			// if (link.hasClass('nowrap')) {
			// 	linkTitle = $('<span class="nowrap">' + linkTitle + '</span>');
			// }
			title.empty().append(linkTitle);
		}).on('mouseleave', function() {
			window.setTimeout(function(){
				title.empty();
			}, removeTitleDelay);		
		});
	} else {
		title.remove();
	}

	// Move Subnav to introduction
	if (isMobile && body.hasClass('page-shop')) {
		var introduction = $('.introduction');
		introduction.empty();
		$('section .subnav').appendTo(introduction);
	}

	// Toggle "Kontakt" on mobile
	if (isMobile) {
		$('nav li.mobile-only').on('click', function(e){
			$(this).parent().find('.current-menu-item').removeClass('current-menu-item');
			$(this).addClass('current-menu-item');
			e.preventDefault();
			body.toggleClass('contact-active');
		});
	}

	// "Read more" for mobile
	if (isMobile) {
		if (body.hasClass('page-shop') || body.hasClass('tax-artist')) {
			// Do nothing
		} else {
			var readMore = $('<div class="readmore">+ Læs mere</div>'),
				readLess = $('<div class="readless">– Læs mindre</div>'),
				section = $('section'),
				paragraphCount = section.find('p').length;
			
			// Add "Read more" if more than two paragraphs
			if (paragraphCount > 2) {
				section.addClass('mobile-abbreviation').find('p:first').addClass('active').append(readMore);
				section.find('p:last').append(readLess);
				readMore.add(readLess).on('click', function() {
					body.toggleClass('mobile-abbreviation-active');
				});
			}
		}
	}
	$(window).resize(function(){
		setImageHeightToGrid();		
	});


	$(window).load(function(){		

		setImageHeightToGrid();

		if (body.hasClass('page-shop')) {

			// Shop: Initiate isotype/masonry
			var $items = $('.shop.items').isotope({
				initLayout: false,
				itemSelector: '.item',
				transitionDuration: '0',
				getSortData: {
					artist: '.artist',
					timestamp: '[data-timestamp]',
					random: function(itemElem) {
						// if ($(itemElem).hasClass('fixed')) {
						// 	return -1;   
						// }
						return Math.random();
					}					
				}
			}).one('layoutComplete', function() {
				$(this).removeClass('loading');
				// After load complete replace image src with better quality
				setImageScrAfterLoad();
			}).isotope({sortBy: 'random'}).isotope();

			// Shop: Sort alphabetical on load
			$('nav.subnav a[data-sort="random"]').addClass('active');
			
			// Shop: Sort items 
			$('nav.subnav a').on('click', function(e){
				e.preventDefault();
				
				// Update class
				$(this).addClass('active').siblings().removeClass('active');

				// Sort depending on input
				var type = $(this).data('sort');
				if (type == 'random') {
					body.removeClass('shop-list-active');
					$items.isotope('shuffle').isotope({
						filter: '.item',
					});
				} else if (type == 'alphabetical') {
					body.removeClass('shop-list-active');
					$items.isotope({
						sortBy: 'artist',
						sortAscending: true,
						filter: '.item',
					});			
				} else if (type == 'recent') {
					body.removeClass('shop-list-active');
					$items.isotope({
						sortBy: 'timestamp',
						sortAscending: false,
						filter: '.item',
					});
				} else if (type == 'list') {
					body.addClass('shop-list-active');
					$items.isotope({
						filter: '.list',
					});
				}
			});


			// Shop: Show image on the artist list view
			if (!isMobile) {
				var artist_placeholder = $('#artist-placeholder');
				$('.shop .list a').on('mouseenter', function() {			
					var artist = $(this).data('id'),
						artist_item = $items.find('article[data-id="' + artist + '"]').first(),
						artisto = artist_item.find('figure').clone().removeAttr('style');			
					artist_placeholder.append(artisto);
				}).on('mouseleave', function() {
					artist_placeholder.empty();
				});
			}
		}



	if (body.hasClass('tax-artist')) {

		// Open event signup in overlay
		$('.buy-btn').on('click', function(e) {
			e.preventDefault();
				
			var itemTitle = $(this).parent().siblings('h2').text(),
				itemArtist = $(this).parent().siblings('.artist').text(),
				itemInfo = $(this).parent().siblings('.info').html();

			// Add item info to form
			var form = $('#overlay form');
			form.find('#item').attr('value', itemTitle + ', ' + itemArtist);
			form.find('#item-info').html(itemTitle + '<br>' + itemArtist + '<br><br>' + itemInfo);
			
			// Show overlay
			if (isMobile) {
				body.addClass('overlay-active').queue(function(next) {
					var formHeight = form.height() + 60;
					form.css({'height' : formHeight });
					next();
				});
			} else {
				body.addClass('overlay-active');
			}
		});

		// Handle submit form for event signup
		$('#overlay form').submit(function(e){
			e.preventDefault();

		    var fullname = $("#fullname").val(),
		    	email = $("#email").val(),
		    	address = $("#address").val(),
		    	cvr = $("#cvr").val(),
		    	item = $("#item").val(),
		    	actionUrl = $(this).attr('action');

		    $.ajax({ 
		         data: {
		         	action: 'order_form',
		         	fullname: fullname,
		         	email: email,
		         	address: address,
		         	cvr: cvr,
		         	item: item,
		         },
		         type: 'post',
		         url: actionUrl,
		         success: function(data) {
					body.addClass('overlay-form-submitted').delay(3000).queue(function(next){
						$(this).removeClass('overlay-active overlay-form-submitted');
						next();
					});
		        }
		    });

		});

		// Clicking in the form doesn't close overlay
		$('form').on('click', function(e) {
			e.stopPropagation();
		});

		$('#overlay').on('click', function() {
			body.removeClass('overlay-active');					
		});

	}




	});


});

