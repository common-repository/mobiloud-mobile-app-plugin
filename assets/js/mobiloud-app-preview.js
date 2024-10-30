jQuery( document ).ready(
	function() {

		alignPreviewHolderLogo();

		var _custom_media     = true,
		_orig_send_attachment = wp.media.editor.send.attachment;

		jQuery( document ).on( 'click', '#ml_preview_upload_image_button--conf, #ml_preview_upload_image_button', function( e ) {
			e.preventDefault();

			const button = jQuery( this );
			let custom_uploader = wp.media({
				title: 'Insert image',
				library : {
					type : 'image'
				},
				multiple: false
			}).on( 'select', function() { // it also has "open" and "close" events
				const attachment = custom_uploader.state().get( 'selection' ).first().toJSON();

				if ( attachment.url ) {
					const previewWrapper = jQuery( '.ml-preview-upload-image-row' );
					previewWrapper.show();
					jQuery( '.ml-preview-image-holder img' ).attr( 'src', attachment.url );
					jQuery( 'input[name="ml_preview_upload_image"]' ).val( attachment.url );

					jQuery( '.mlconf__logo-image-wrapper' ).show();
					jQuery( '.mlconf__logo-image-wrapper img' ).attr( 'src', attachment.url );
				}
			}).open();
		} );

		jQuery( document ).on( 'click', '.mlsw__app-design .mlsw__button--blue', function( e ) {
			e.preventDefault();
			const nextPageUrl = jQuery( this ).attr( 'href' );
			const imageUrl = jQuery( 'input[name="ml_preview_upload_image"]' ).val();
			const brandColor = jQuery( '#ml_preview_theme_color' ).val();

			jQuery.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					imageUrl,
					brandColor,
					action: 'ml_save_data_during_config'
				}
			} ).done( () => {
				window.location.href = nextPageUrl;
			} )
		} );

		jQuery( document ).on( 'click', '.mlsw__app-design .ml-preview-image-remove-btn', function( e ) {
			e.preventDefault();
			const previewWrapper = jQuery( '.ml-preview-upload-image-row' );
			previewWrapper.hide();
			jQuery( '.ml-preview-image-holder img' ).attr( 'src', '' );
			jQuery( 'input[name="ml_preview_upload_image"]' ).val( '' );
		} );

		jQuery( "#mlconf__remove-logo" ).click(
			function(e) {
				e.preventDefault();
				var confirmRemove = confirm( 'Are you sure you want to remove the image?' );
				if (confirmRemove) {
					jQuery( '.mlconf__logo-image-wrapper' ).hide();
					jQuery( '.mlconf__logo-image-wrapper img' ).attr( 'src', '' );
					jQuery( 'input[name="ml_preview_upload_image"]' ).val( '' );
				}
			}
		);

		jQuery( "#ml_preview_upload_image" ).keyup(
			function() {
				$ml_notify_element = jQuery( this );
				loadPreviewImage();
				ml_loadPreview();
			}
		);

		jQuery( "input[name='ml_article_list_view_type']" ).click(
			function() {
				ml_loadPreview();
			}
		);

		jQuery( "input[name='ml_datetype']" ).click(
			function() {
				ml_loadPreview();
			}
		);

		jQuery( "#ml_dateformat" ).keyup(
			function() {
				ml_loadPreview();
				updateHometype();

			}
		);

		var link_color = jQuery( '#ml_preview_theme_color' );
		link_color.wpColorPicker(
			{
				change: function(event, ui) {
					pickColor( link_color.wpColorPicker( 'color' ) );
					ml_loadPreview();
				},
				clear: function() {
					pickColor( '' );
				}
			}
		);
		jQuery( '#ml_preview_theme_color' ).click( toggle_text );

		toggle_text();

		ml_loadPreview();
	}
);

var alignPreviewHolderLogo = function() {
	var imageHolder = jQuery( ".ml-preview-image-holder" );
	var image       = jQuery( "img", imageHolder );
	if (imageHolder.length && image.length) {
		if (image.height > image.width) {
			image.height = '100%';
			image.width  = 'auto';
		}
	}
};

var articleScrol;
var loadIScroll = function() {
	articleScroll = new IScroll(
		'.ml-preview-article-list',
		{
			scrollbars: true,
			fadeScrollbars: true,
			mouseWheel: true
		}
	);
};

var default_color     = '1e73be';
var color_initialized = false;

function pickColor(color) {
	jQuery( '#ml_preview_theme_color' ).val( color );
	if (color_initialized) {
		$ml_notify_element = jQuery( '.wp-picker-container' );
	}
	color_initialized = true;
}
function toggle_text() {
	link_color = jQuery( '#ml_preview_theme_color' );
	if (link_color.length) {
		if (link_color.val() === '' || '' === link_color.val().replace( '#', '' )) {
			link_color.val( default_color );
			pickColor( default_color );
		} else {
			pickColor( link_color.val() );
		}
	}
}

var alignPreviewLogo = function(logo) {
	var logoHeight   = jQuery( logo ).height();
	var logoWidth    = jQuery( logo ).width();
	var holderHeight = jQuery( logo ).parent().height();
	var holderWidth  = jQuery( logo ).parent().width();
	jQuery( logo ).css( 'margin-top', (holderHeight - logoHeight) / 2 );
	if (jQuery( ".ml-preview" ).hasClass( 'ios' )) {
		jQuery( logo ).css( 'margin-left', (holderWidth - logoWidth) / 2 );
	} else {
		jQuery( logo ).css( 'margin-left', '0' );
	}
};


var updateHometype = function() {
	var hometype = jQuery( "#hidden_homepagetype" ).val();

	if (hometype == "ml_home_article_list_enabled") {
		jQuery( ".ml-preview-article" ).show();
		jQuery( "#ml-page-placeholder" ).hide();
	} else {
		jQuery( ".ml-preview-article" ).hide();
		jQuery( "#ml-page-placeholder" ).show();
	}
}

var $ml_notify_element = false;
var ml_loadPreview     = function() {
	if ( ! jQuery('.ml-preview-app').length ) {
		return;
	}
	var data = {
		action: 'ml_preview_app_display',
		ml_preview_upload_image: jQuery( "#ml_preview_upload_image" ).val(),
		ml_preview_theme_color: jQuery( "#ml_preview_theme_color" ).val(),
		ml_preview_os: jQuery( "input[name='ml_preview_os']:checked" ).val(),
		ml_article_list_view_type: jQuery( "input[name='ml_article_list_view_type']:checked" ).val(),
		ml_datetype: jQuery( "input[name='ml_datetype']:checked" ).val(),
		ml_dateformat: jQuery( "input[name='ml_dateformat']" ).val(),
		ml_nonce: jQuery( '#ml_nonce' ).val(),
	};
	jQuery( ".ml-preview-app" ).append( jQuery( "#ml_preview_loading" ) );

	jQuery.post(
		ajaxurl,
		data,
		function(response) {
			// saving the result and reloading the div
			jQuery( ".ml-preview-app" ).html( response ).fadeIn().slideDown(
				500,
				function() {
					jQuery( '.ml-preview-logo' ).load(
						function() {
							alignPreviewLogo( jQuery( '.ml-preview-logo' ) );
						}
					);

					jQuery( '.ml-preview-img' ).load(
						function() {
							var viewType = jQuery( "input[name='ml_article_list_view_type']:checked" ).val();
							var cropWidth;
							switch (viewType) {
								default:
								case 'extended':
									cropWidth = 253;
									if (jQuery( "input[name='ml_preview_os']:checked" ).val() === 'android') {
										cropWidth = 287;
									}
									break;
								case 'compact':
									cropWidth = '113';
									if (jQuery( "input[name='ml_preview_os']:checked" ).val() === 'android') {
										cropWidth = 137;
									}
									break;
							}

							cropPostImages( cropWidth );
						}
					);

					loadIScroll();
					updateHometype();

					if ($ml_notify_element) {
						jQuery( '.notifyjs-corner' ).empty();
						jQuery.notify( 'Updated', { position:'top right', className: 'ml-success' } );
					}
					ml_preview_initiated = true;
				}
			);
		}
	);
};

var cropPostImages = function(width) {
	jQuery( '.ml-post-img-wrapper' ).css( 'width', width ).css( 'height', 100 );
	jQuery( '.ml-post-img-wrapper' ).imgLiquid( {fill: true} );
};

var loadPreviewImage = function() {
	const imageUrl = jQuery( "#ml_preview_upload_image" ).val();
	const imageEl = jQuery( '.mlconf__logo-image' );
	const imageWrapperEl = jQuery( '.mlconf__logo-image-wrapper' );

	if ( ! imageUrl ) {
		return;
	}

	if ( imageUrl.length > 0) {
		if ( imageEl.length > 0 ) {
			jQuery( ".mlconf__logo-image" ).show();
			jQuery( ".mlconf__logo-image" ).attr( 'src',  imageUrl );
		} else {
			imageWrapperEl.append( `<img class="mlconf__logo-image" src="${ imageUrl }" />` );
		}
		alignPreviewHolderLogo();
	} else {
		jQuery( ".mlconf__logo-image" ).hide();
	}
};
