jQuery(
	function() {
		if (jQuery.fn.areYouSure) {
			jQuery( '#get_started_design form' ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(#ml_preview_upload_image)"
				}
			);

			jQuery( "#get_started_menu_config form" ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(select):not(input[type=text])"
				}
			);

			jQuery( "#ml_settings_general form" ).areYouSure();

			jQuery( "#ml_settings_editor form" ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(select)"
				}
			);

			jQuery( "#ml_settings_membership form" ).areYouSure();

			jQuery( "#ml_settings_license form" ).areYouSure();

			jQuery( "#ml_push_settings form" ).areYouSure();

			jQuery( "#ml_settings_advertising form" ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button]):not(#ml_ad_banner_position_select):not(#preview_popup_post_select)"
				}
			);

			jQuery( '#ml_settings_subscription form' ).areYouSure(
				{
					'fieldSelector': ":input:not(input[type=submit]):not(input[type=button])"
				}
			);

		}

		if (jQuery( '.nav-tab[data-tab]' ).length) {
			jQuery( '.nav-tab[data-tab]' ).on(
				'click',
				function() {
					var $tab = jQuery( jQuery( this ).data( 'tab' ) );
					$tab.show();
					$tab.siblings( '.nav-tab-content' ).hide();
					jQuery( this ).addClass( 'nav-tab-active' );
					jQuery( this ).siblings( '.nav-tab' ).removeClass( 'nav-tab-active' );
					return false;
				}
			)
		}

		if (jQuery( '.ml-value-get' ).length) {
			jQuery( '.ml-value-get' ).on(
				'change',
				function() {
					var $destination = jQuery( this ).closest( 'td' ).find( '.ml-value-set' );
					if ($destination.length) {
						$destination.text( jQuery( this ).val() );
					}
				}
			).trigger( 'change' );
		}

		if (jQuery( '.ml_load_ajax' ).length) {
			jQuery( '.ml_load_ajax' ).each(
				function() {
					var $that = jQuery( this );
					var data  = {
						action: 'ml_load_ajax',
						what: jQuery( this ).data( 'ml_what' ),
						ml_nonce: jQuery( '#ml_nonce_load_ajax' ).val(),
					};
					jQuery.post(
						ajaxurl,
						data,
						function(response) {
							if (response.data !== undefined) {
								$that.replaceWith( response.data );
								if (response.chosen) {
									jQuery( '#' + response.chosen ).chosen( {} );
								}
								if (response.show) {
									jQuery( response.show ).show();
								}
								if (response.ul_name) {
									jQuery( response.ul_name ).html( response.ul );
								}
							}
						}
					);
				}
			)
		}

		if (jQuery( '.ml-colorbox' ).length) {
			jQuery( '.ml-colorbox' ).each(
				function() {

					var $link_color = jQuery( this );
					$link_color.wpColorPicker(
						{
							change: function(event, ui) {
								pick_text_color( $link_color.wpColorPicker( 'color' ), jQuery( this ) );
							},
							clear: function() {
								pick_text_color( '', jQuery( this ) );
							}
						}
					);
					$link_color.trigger( 'click' ).trigger( 'keyup' );

					toggle_text_color( $link_color );
				}
			)
		}
		if (jQuery( '.ml-iconbox' ).length) {
			var $icon_input = null;
			var _custom_media = true;
			var _orig_send_attachment = wp.media.editor.send.attachment;
			jQuery( '.ml-iconbox' ).each( function() {
				var $block = jQuery(this);
				var $button = $block.find('.icon-load');
				var $clean = $block.find('.icon-clean');
				var $default = $block.find('.icon-default');
				var $input = $block.find('.icon-input');
				var $view = $block.find('.icon-view');
				var $tabIcon = jQuery( '.nav-tab-active' ).find( '.mlconf__tab-menu-icon' );
				var timer = null;
				var _swal = null;
				var icon_preview = function( url ) {
					if ( timer ) {
						clearTimeout( timer );
					}
					timer = setTimeout( function() { $view.attr( 'src', url ); }, 200 );

				}
				var icon_clear = function() {
					$view.attr( 'src', '' );
					$tabIcon.attr( 'src', '' );
				}

				$input.on('keyup change paste', function() {
					icon_preview( $input.val() );
				})

				$button.click(
					function(e) {
						var send_attachment_bkp         = wp.media.editor.send.attachment;
						_custom_media                   = true;
						wp.media.editor.send.attachment = function(props, attachment) {
							wp.media.editor.send.attachment = send_attachment_bkp;
							if (_custom_media) {
								var url = attachment.url;
								if ( attachment && attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.width && attachment.sizes.thumbnail.width < attachment.width ) {
									url = attachment.sizes.thumbnail.url;
								}
								$input.val( url );
								$tabIcon.attr( 'src', url );
								icon_preview( url );
								_custom_media = false;
							} else {
								return _orig_send_attachment.apply( this, [props, attachment] );
							}
						};
						wp.media.editor.open( $button );
						return false;
					}
				);
				$clean.click(
					function(e) {
						$input.val('');
						icon_clear();
						return false;
					}
				);
				$default.click(
					function(e) {
						$icon_input = $input;
						var html = ( ml_default_icons || [] ).map(function(item){return '<a href="#" class="ml-icon-selector"><img class="ml-icon-image" src="'+item+'"></a>'}).join(' ');
						var wrapper = document.createElement('div');
						wrapper.innerHTML = '<div class="ml-icon-selector-wrap">' + html + '</div>';
						swal({
							title: 'Please choose an icon',
							content: wrapper,
							buttons: {}
						});
					}
				);
				icon_preview( $input.val() );
			})
			jQuery(document).on('click', '.ml-icon-selector', function() {
				if ( $icon_input ) {
					const imageUrl = jQuery( this ).find('img').attr('src');
					$icon_input.val( imageUrl ).trigger('change');
					jQuery( '.nav-tab-active' ).find( '.mlconf__tab-menu-icon' ).attr( 'src', imageUrl );
				}
				swal.close();
				return false;
			})
		}

		( function( $ ) {
			var $button = $('.upload-logo-button');
			var $input = $('.upload-logo-input');
			var $clean = $('.upload-logo-clean');
			var $view = $('.upload-logo-view');
			var _orig_send_attachment = wp.media.editor.send.attachment;
			var timer = null;

			var icon_preview = function( url ) {
				if ( timer ) {
					clearTimeout( timer );
				}
				timer = setTimeout( function() {
					$view.attr( 'src', url );
					if ( '' !== url ) {
						$view.parent().parent().show();
					} else {
						$view.parent().parent().hide();
					}
					}, 200 );
			}
			var icon_clear = function() {
				$view.attr( 'src', '' );
				$view.parent().parent().hide();
			}

			$input.on('keyup change paste', function() {
				icon_preview( $input.val() );
			})
			$clean.click(
				function(e) {
					$input.val('');
					icon_clear();
					return false;
				}
			);

			$button.on('click',
				function(e) {
					var send_attachment_bkp         = wp.media.editor.send.attachment;
					_custom_media                   = true;
					wp.media.editor.send.attachment = function(props, attachment) {
						wp.media.editor.send.attachment = send_attachment_bkp;
						if (_custom_media) {
							var url = attachment.url;
							$input.val( url ).trigger('change');
							icon_preview( url );
							_custom_media = false;
						} else {
							return _orig_send_attachment.apply( this, [props, attachment] );
						}
					};
					wp.media.editor.open( $button );
					return false;
				}
			)
		} )( jQuery);

	}
);

var text_default_color = '1e73be';
function pick_text_color(color, $link_color) {
	$link_color.val( color );
}

function toggle_text_color($link_color) {
	if ($link_color.length) {
		if ($link_color.val() === '' || '' === $link_color.val().replace( '#', '' )) {
			var default_color = 'undefined' != typeof $link_color.data( 'color' ) ? $link_color.data( 'color' ) : text_default_color;
			$link_color.val( default_color );
			pick_text_color( default_color, $link_color );
		} else {
			pick_text_color( $link_color.val(), $link_color );
		}
	}
}
