var fineTextAreas = {};

function ml_add_editor( field, mode ) {
	var args = {};
	if ( ml_codemirror[mode] ) {
		jQuery.extend( args, ml_codemirror[mode].codemirror || {} );
	} else {
		jQuery.extend( args, wp.codeEditor.defaultSettings.codemirror || {} );
	}
	if ( mode ) {
		args['mode'] = mode;
	}
	var fine_editor = wp.CodeMirror.fromTextArea(
		jQuery( field ).get( 0 ),
		args
	);
	return fine_editor;
};

jQuery( document ).ready(
	function() {
		var ml_niceEditor = false;
		var track = function( action ) {
			if ( 'function' === typeof Intercom ) {
				Intercom("trackUserEvent", action);
			}
		}
		check_advert_type();

		var ml_update_initial_value = function($field) {
			var value = 'checkbox' == $field.attr( 'type' ) ? $field.is( ':checked' ) : $field.val();
			$field.data( 'ays-orig', value );
		};

		jQuery( "#ml_admin_post_customization_select" ).change(
			function(e) {
				if ( wp.codeEditor ) {
					if (jQuery( this ).val() !== '') {

						if ( ml_niceEditor ) {
							ml_niceEditor.toTextArea();
						}

						jQuery( ".ml-show" ).removeClass( 'ml-show' );
						jQuery( "textarea[name='" + jQuery( this ).val() + "']" ).addClass( 'ml-show' );

						ml_niceEditor = ml_add_editor(
							document.querySelector( "textarea[name='" + jQuery( this ).val() + "']" ),
							jQuery( this ).find( 'option:selected' ).data( 'type' )
						);
					}
				}
			}
		);

		jQuery( ".ml-save-editor-btn" ).click(
			function(e) {
				e.preventDefault();
				ml_niceEditor.save();
				var selected_editor = jQuery( "#ml_admin_post_customization_select" ).val();
				if (selected_editor !== '') {
					var data = {
						action: 'ml_save_editor',
						editor: selected_editor,
						value: jQuery( "textarea[name='" + selected_editor + "']" ).val(),
						ml_nonce: jQuery( '#ml_nonce_editor' ).val(),
					};
					jQuery.post(
						ajaxurl,
						data,
						function(response) {
							if ( 1 == JSON.parse( response ) ) {
								ml_update_initial_value( jQuery( "textarea[name='" + selected_editor + "']" ) );
								jQuery( 'form' ).trigger( 'checkform.areYouSure' );
								sweetAlert( 'Saved!', '', 'success' );
								track('settings_editor');
							} else {
								sweetAlert( 'Error!', '', 'error' );
							}
						}
					);
				}
			}
		);

		jQuery( ".ml-save-editor-embed-btn" ).click(
			function(e) {
				e.preventDefault();
				fineTextAreas['ml_embedded_page_css'].save();

				var items = {};
				jQuery( '.ml-settings-embed' ).each(
					function() {
						if (jQuery( this ).is( ':checkbox' )) {
							items[jQuery( this ).attr( 'name' )] = jQuery( this ).is( ':checked' ) ? 1 : 0;
						} else {
							items[jQuery( this ).attr( 'name' )] = jQuery( this ).val();
						}
					}
				);
				var selected_editor = 'ml-settings-embed';
				var data            = {
					action: 'ml_save_editor_embed',
					items: items,
					ml_nonce: jQuery( '#ml_nonce_editor_embed' ).val(),
				};
				jQuery.post(
					ajaxurl,
					data,
					function(response) {
						if ( 1 == JSON.parse( response ) ) {
							// save new state for are-you-sure
							jQuery( '.ml-settings-embed' ).each(
								function() {
									ml_update_initial_value( jQuery( this ) );
								}
							)
							jQuery( 'form' ).trigger( 'checkform.areYouSure' );

							sweetAlert( 'Saved!', '', 'success' );
						} else {
							sweetAlert( 'Error!', '', 'error' );
						}
					}
				);
			}
		);

		jQuery( "#ml_ad_banner_position_select" ).change(
			function(e) {
				if (jQuery( this ).val() !== '') {
					jQuery( ".ml-show" ).removeClass( 'ml-show' );
					var textarea = "textarea[name='" + jQuery( this ).val() + "']";
					jQuery( textarea + ', ' + textarea + ' + input' + ', ' + textarea + ' + input + label' ).addClass( 'ml-show' );
				}
			}
		);

		jQuery( ".ml-save-banner-btn" ).click(
			function(e) {
				e.preventDefault();
				var selected_position = jQuery( "#ml_ad_banner_position_select" ).val();
				if (selected_position !== '') {
					var data = {
						action: 'ml_save_banner',
						position: selected_position,
						value: jQuery( "textarea[name='" + selected_position + "']" ).val(),
						app_sub_show: jQuery( "textarea[name='" + selected_position + "'] + input" ).is( ':checked' ) ? 1 : 0,
						ml_nonce: jQuery( '#ml_nonce_save_banner' ).val(),
					};
					jQuery.post(
						ajaxurl,
						data,
						function(response) {
							if ( 1 == JSON.parse( response) ) {
								jQuery( 'form' ).removeClass( 'dirty' );
								sweetAlert( 'Saved!', '', 'success' );
							} else {
								sweetAlert( 'Error!', '', 'error' );
							}
						}
					);
				}
			}
		);

		if (jQuery( '.ml_admob_size_select' ).length) {
			jQuery( '.ml_admob_size_select' ).on(
				'change',
				function() {
					var height = jQuery( this ).find( 'option:selected' ).data( 'height' );
					var size   = jQuery( this ).find( 'option:selected' ).data( 'size' );
					jQuery( this ).closest( '.ml_ad_interval' ).find( '.ml_admob_size_notice' ).text( height );
					jQuery( this ).closest( '.ml_ad_interval' ).find( '.ml_gdfp_size_notice' ).text( size );
				}
			).trigger( 'change' );
		}
	}
);

var check_advert_type_init = false;
var check_advert_type      = function() {
	if (jQuery( "#ml_advertising_platform" ).length) {
		if ( ! check_advert_type_init) {
			jQuery( "#ml_advertising_platform" ).change(
				function() {
					check_advert_type();
				}
			);
			check_advert_type_init = true;
		}
		var advert_type = jQuery( "#ml_advertising_platform" ).val();
		jQuery( ".ml_native_ads_wrap, .ml_admob_ads_wrap, .ml_gdfp_ads_wrap" ).hide();

		if ('admob' == advert_type) {
			jQuery( ".ml_admob_ads_wrap" ).show();
		} else if ('gdfp' == advert_type) {
			jQuery( ".ml_gdfp_ads_wrap" ).show();
		}
	}
};

// paywall screen editors.
(function($){
	"use strict";
	$( document ).ready(
		function() {
			if ( $( '.ml-editor-area-html, .ml-editor-area-css' ).length && wp.CodeMirror ) {
				$( '.ml-editor-area-html' ).each( function() {ml_add_editor( this, 'htmlmixed' );} )
				$( '.ml-editor-area-css' ).each( function() {
					fineTextAreas[this.name] = ml_add_editor( this, 'css' );
				} )
			}
		}
	);
})( jQuery );

(function() {
	jQuery( document ).ready(
		function($) {
			var $checkboxes, $customization_select, $post_div, $post_page, $preview_window, checked, e, openPreviewPopup, preview_window, saveCode, saveOptions, _i, _len;
			$post_div             = $( '#ml_admin_post' );
			$post_page            = $( '#ml_admin_post_page' );
			$customization_select = $post_page.find( 'select' ).first();
			preview_window        = null;
			$preview_window       = null;
			var get_src = function() {
				var src = $( '#preview_popup_post_select' ).val();
				if ( '-custom-' === src ) {
					src = $( '#preview_popup_post_url' ).val();
				}
				return src;
			}
			if ( $( '#preview_popup_post_url' ).length ) {
				$( '#preview_popup_post_url' ).hide();
			}

			$( '.ml_open_preview_btn' ).click(
				function(e) {
					e.preventDefault();
					var src = get_src();
					if ( src.indexOf( '/v2/list' ) > 0 ) {
						$( '#preview_popup_content .iphone5s_device, #preview_popup_content .ipad2_device' ).removeClass( 'is_post' );
					} else {
						$( '#preview_popup_content .iphone5s_device, #preview_popup_content .ipad2_device' ).addClass( 'is_post' );
					}
					if ($( this ).hasClass( 'ml-preview-phone-btn' )) {
						return openPreviewPopup( src, 'iphone' );
					} else if ($( this ).hasClass( 'ml-preview-tablet-btn' )) {
						return openPreviewPopup( src, 'ipad' );
					}
				}
			);
			// Current Editor select.
			$( '#ml_admin_post_customization_select' ).change(
				function() {
					var src = get_src();
					return $preview_window != null ? $preview_window.find( 'iframe' ).attr( 'src', src ) : void 0;
				}
			);
			// preview choice select.
			$( '#preview_popup_post_select' ).change(
				function() {
					if ( '-custom-' === $( '#preview_popup_post_select' ).val() ) {
						$( '#preview_popup_post_url' ).show();
					} else {
						$( '#preview_popup_post_url' ).hide();
					}
				}
			);
			openPreviewPopup = function(src, device, width, height) {
				var $preview_content, preview_css_links;
				if (device == null) {
					device = 'iphone';
				}
				if (width == null) {
					width = 465;
				}
				if (height == null) {
					height = 894;
				}
				$preview_content = $( '#preview_popup_content .iphone5s_device' );
				if (device === 'ipad') {
					$preview_content = $( '#preview_popup_content .ipadmini_device' );
					width            = 540;
					height           = 750;
					device           = 'ipad2';
				}
				if (device === 'ipad2') {
					$preview_content = $( '#preview_popup_content .ipad2_device' );
					width            = 490;
					height           = 686;
				}
				preview_css_links                       = $( "#mobiloud_admin_post-css, #mobiloud-app-preview-css" );
				preview_window                          = window.open( "", "popupWindow", "width=" + width + ", height=" + height + ", scrollbars=no, location=no,titlebar=no,toolbar=no,status=no,menubar=no,resizable=no" );
				preview_window.document.body.className += ' ml2-preview';
				$preview_window                         = $( preview_window.document.body );
				$preview_window.html( "" );
				preview_css_links.each(
					function(idx, e) {
						return $preview_window.append( $( e ).clone() );
					}
				);
				$preview_window.append( $preview_content.clone() );
				$preview_window.css( 'width', width );
				$preview_window.css( 'height', height );
				$preview_window.css( 'min-width', 'auto' );
				$preview_window.css( 'min-height', 'auto' );
				console.log( "src: " + src );
				return $preview_window.find( 'iframe' ).attr( 'src', src );
			};
			$customization_select.change(
				function() {
					var action_name, code;
					action_name = $( this ).find( 'option:selected' ).val();
					code        = $post_page.find( "textarea[name='" + action_name + "']" ).val();
					code        = stripslashes( code );
					return $( '#ml_admin_post_textarea' ).val( code );
				}
			);
			saveCode = function(before, after) {
				var code, data;
				if (typeof before === "function") {
					before();
				}
				code = $( '#ml_admin_post_textarea' ).val();
				data = {
					action: 'ml_admin_post_save_code',
					customization_name: $customization_select.find( 'option:selected' ).val(),
					code: code
				};
				return $.post(
					ajaxurl,
					data,
					function(response) {
						var action_name;
						action_name = $customization_select.find( 'option:selected' ).val();
						$post_page.find( "textarea[name='" + action_name + "']" ).val( code );
						if (typeof after === "function") {
							after();
						}
						return win.location.reload();
					}
				);
			};
			$post_div.find( "input[type='submit']" ).click(
				function() {
					var label, saving_label,
					_this        = this;
					label        = $( this ).data( 'label' );
					saving_label = $( this ).data( 'saving-label' );
					return saveCode(
						function() {
							return $( _this ).val( saving_label ).attr( 'disabled', true );
						},
						function() {
							return $( _this ).val( label ).attr( 'disabled', false );
						}
					);
				}
			);
			$checkboxes = $( "#ml_admin_post_options input[type='checkbox']" );
			saveOptions = function() {
				var checked, data, e, name, _i, _len;
				data = {
					action: 'ml_admin_post_save_options'
				};
				for (_i = 0, _len = $checkboxes.length; _i < _len; _i++) {
					e          = $checkboxes[_i];
					name       = $( e ).attr( 'name' );
					checked    = $( e ).is( ':checked' );
					data[name] = checked;
				}
				console.log( data );
				return $.ajax(
					{
						url: ajaxurl,
						data: data,
						type: 'POST',
						dataType: 'html',
						success: function(response) {}
					}
				);
			};
			for (_i = 0, _len = $checkboxes.length; _i < _len; _i++) {
				e       = $checkboxes[_i];
				checked = $( e ).data( 'checked' );
				if (checked) {
					$( e ).attr( 'checked', true );
				} else {
					$( e ).attr( 'checked', false );
					$( e ).removeAttr( 'checked' );
				}
			}
			return $( "#ml_admin_post_options input[type='submit']" ).click(
				function() {
					return saveOptions();
				}
			);
		}
	);

}).call( this );
