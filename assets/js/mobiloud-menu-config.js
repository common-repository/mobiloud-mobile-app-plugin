jQuery(document).ready(function() {
	jQuery( '.ml-color .color-picker' ).wpColorPicker();

	jQuery(".ml-menu-holder").sortable({
		update: function( event, ui ) {
			jQuery("#get_started_menu_config form").trigger('setDirty.areYouSure');
		}
	});

	jQuery("select[name='ml-tax-group']").change(function() {
		var group = jQuery(this).val();
		if(group !== '') {
			jQuery("select[name='ml-terms']").find("option[value!='']").remove();
			jQuery(".ml-tax-group-row").hide();
			var data = {
				action: 'ml_tax_list',
				group: group,
				ml_nonce: jQuery( '#ml_nonce' ).val(),
			};
			jQuery.post(ajaxurl, data, function(response) {
				if(response.terms !== undefined) {
					for(term_id in response.terms) {
						var term = response.terms[term_id];
						jQuery("select[name='ml-terms']").append(jQuery('<option></option>').val(term.id).attr('title', term.title).html(term.fullname));
					}
					jQuery(".ml-tax-group-row").show();
				}
			});
		} else {
			jQuery(".ml-tax-group-row").hide();
			jQuery("select[name='ml-terms']").find('option').remove();
		}
	});

	jQuery(".ml-add-term-btn").click(function(e) {
		e.preventDefault();
		var selected_term = jQuery(".ml-select-add[name='ml-terms']").val();
		var selected_term_text = jQuery(".ml-select-add[name='ml-terms'] option:selected").attr('title');
		var selected_tax = jQuery(".ml-select-add[name='ml-tax-group']").val();
		if(selected_term !== '' && jQuery(".ml-menu-terms-holder li[rel='"+selected_term+"']").length <= 0) {
			var new_li = jQuery("<li>")
			.attr('rel', selected_term)
			.html("<span class='dashicons-before dashicons-menu'></span>"+selected_term_text)
			.appendTo(jQuery(".ml-menu-terms-holder"));
			jQuery("<input/>")
			.attr('name', 'ml-menu-terms[]')
			.attr('value', selected_tax + "=" + selected_term)
			.attr('type', 'hidden')
			.appendTo(new_li);
			jQuery("<a>")
			.attr('href', '#')
			.attr('class', 'dashicons-before dashicons-trash ml-item-remove')
			.appendTo(new_li);
		}
	});

	jQuery(".ml-add-tag-btn").click(function(e) {
		e.preventDefault();
		var selected_term = jQuery(".ml-select-add[name='ml-tags']").val();
		var selected_term_text = jQuery(".ml-select-add[name='ml-tags'] option:selected").text();
		if(selected_term !== '' && jQuery(".ml-menu-tags-holder li[rel='"+selected_term+"']").length <= 0) {
			var new_li = jQuery("<li>")
			.attr('rel', selected_term)
			.html("<span class='dashicons-before dashicons-menu'></span>"+selected_term_text)
			.appendTo(jQuery(".ml-menu-tags-holder"));
			jQuery("<input/>")
			.attr('name', 'ml-menu-tags[]')
			.attr('value', selected_term)
			.attr('type', 'hidden')
			.appendTo(new_li);
			jQuery("<a>")
			.attr('href', '#')
			.attr('class', 'dashicons-before dashicons-trash ml-item-remove')
			.appendTo(new_li);
		}
	});

	jQuery(".ml-add-category-btn").click(function(e) {
		e.preventDefault();
		var selected_cat = jQuery(".ml-select-add[name='ml-category']").val();
		var selected_cat_text = jQuery(".ml-select-add[name='ml-category'] option:selected").attr('title');
		if(selected_cat !== '' && jQuery(".ml-menu-categories-holder li[rel='"+selected_cat+"']").length <= 0) {
			var new_li = jQuery("<li>")
			.attr('rel', selected_cat)
			.html("<span class='dashicons-before dashicons-menu'></span>"+selected_cat_text)
			.appendTo(jQuery(".ml-menu-categories-holder"));
			jQuery("<input/>")
			.attr('name', 'ml-menu-categories[]')
			.attr('value', selected_cat)
			.attr('type', 'hidden')
			.appendTo(new_li);
			jQuery("<a>")
			.attr('href', '#')
			.attr('class', 'dashicons-before dashicons-trash ml-item-remove')
			.appendTo(new_li);
		}
	});

	jQuery(".ml-add-page-btn").click(function(e) {
		e.preventDefault();
		var selected_cat = jQuery(".ml-select-add[name='ml-page']").val();
		var selected_cat_text = jQuery(".ml-select-add[name='ml-page'] option:selected").text();
		if(selected_cat !== '' && jQuery(".ml-menu-pages-holder li[rel='"+selected_cat+"']").length <= 0) {
			var new_li = jQuery("<li>")
			.attr('rel', selected_cat)
			.html("<span class='dashicons-before dashicons-menu'></span>"+selected_cat_text)
			.appendTo(jQuery(".ml-menu-pages-holder"));
			jQuery("<input/>")
			.attr('name', 'ml-menu-pages[]')
			.attr('value', selected_cat)
			.attr('type', 'hidden')
			.appendTo(new_li);
			jQuery("<a>")
			.attr('href', '#')
			.attr('class', 'dashicons-before dashicons-trash ml-item-remove')
			.appendTo(new_li);
		}
	});

	jQuery(".ml-add-link-btn").click(function(e) {
		e.preventDefault();
		var link_title = jQuery("#ml_menu_url_title").val();
		var link_url = jQuery("#ml_menu_url").val();
		if ('' == link_title) {
			sweetAlert('Empty title', '', 'error');

		} else if ('' == link_url) {
			sweetAlert('Empty link', '', 'error');
		} else {
			// Copyright (c) 2010-2013 Diego Perini, MIT licensed
			// https://gist.github.com/dperini/729294
			// see also https://mathiasbynens.be/demo/url-regex
			// modified to allow protocol-relative URLs
			if (/^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test( link_url )) {
				if(link_title !== '' && link_url !== '' && jQuery(".ml-menu-links-holder li[rel='"+link_title+"']").length <= 0) {
					var new_li = jQuery("<li>")
					.attr('rel', link_url)
					.html("<span class='dashicons-before dashicons-menu'></span>"+link_title+" - <span class='ml-sub-title'>"+trim_string(link_url, 50)+"</span>")
					.appendTo(jQuery(".ml-menu-links-holder"));
					jQuery("<input/>")
					.attr('name', 'ml-menu-links[]')
					.attr('value', link_title + ":=:" + link_url)
					.attr('type', 'hidden')
					.appendTo(new_li);
					jQuery("<a>")
					.attr('href', '#')
					.attr('class', 'dashicons-before dashicons-trash ml-item-remove')
					.appendTo(new_li);
				}
			} else {
				sweetAlert('Incorrect link: ' + link_url, '', 'error');
			}
		}
	});

	jQuery( '.ml-menu-holder' ).on( 'click', '.ml-item-remove', function( e ) {
		e.preventDefault();
		jQuery(this).parents('li').remove();
	});

	// Tabbed navigation
	jQuery( '#ml-tabnav-tabs a' ).on(
		'click',
		function( ev ) {
			ev.preventDefault();
			var tabContent = jQuery( this ).attr('href');
			jQuery( '#ml-tabnav-tabs a.nav-tab-active' ).removeClass( 'nav-tab-active' );
			jQuery( '#ml-navtab-contents .active' ).removeClass( 'active' );
			jQuery( this ).addClass('nav-tab-active');
			jQuery( tabContent ).addClass( 'active' );

		}
	);

	jQuery( "#ml-tabnav-tabs" ).sortable({
		update: function( event, ui ) {
			// update order
			update_tabnav_order();
		}
	});

	function update_tabnav_order() {
		var order = '';
		jQuery( '#ml-tabnav-tabs a' ).each( function() {
			var tab = jQuery( this ).attr('id').split('tabnav-tab-');
			order += tab[1] + ',';
		} );
		order = order.slice( 0, -1 );
		jQuery( '#ml-tabnav-order' ).val( order );
	}

	jQuery( '#ml-navtab-contents .color-picker' ).wpColorPicker(
		{
			change: function(event, ui) {
				var target = jQuery( this ).parents( ".tabnav-content" );
				target.attr( 'style', 'background-color: ' + jQuery( this ).wpColorPicker( 'color' ) );
			},
			clear: function() {

			}
		}
	);

	jQuery( '.tab-color .color-picker' ).wpColorPicker();

	jQuery( '#ml-login-settings .color-picker' ).wpColorPicker();

	jQuery( '#ml_tabbed_navigation_enabled' ).on( 'change', function() {
		const tabbedMenuWrapper = jQuery('.mlconf__row-toggle');
		const tabbedMenuWrapperInverse = jQuery('.mlconf__row-toggle--inverse');
		const isChecked = jQuery( this ).is( ':checked' );

		if ( isChecked ) {
			tabbedMenuWrapper.show( '300' );
			tabbedMenuWrapperInverse.hide( '300' );
		} else {
			tabbedMenuWrapper.hide( '300' );
			tabbedMenuWrapperInverse.show( '300' );
		}
	} );

	jQuery( document ).on(
		'keyup', 'input.ml-tab-label',
		function() {
			var tab_id = jQuery( this ).parents( ".tabnav-content" ).attr( 'id' ).split('tabnav-');
			jQuery( '.nav-tab-active .mlconf__tab-menu-label' ).text( jQuery( this ).val() );
		}
	);

	jQuery( 'input.ml-tab-icon' ).on(
		'change',
		function() {
			var tab_id = jQuery( this ).parents( ".tabnav-content" ).attr( 'id' ).split('tabnav-');
			var target = jQuery( '#tabnav-tab-' + tab_id[1] + ' i' );
			target.attr( 'class', 'dashicons ' + jQuery( this ).val() );
		}
	);

	jQuery( 'select.ml-tab-type' ).on(
		'change',
		function() {
			var type = jQuery( this ).val();
			var tabContent = jQuery( this ).parents( ".tabnav-content" );
			tabContent.find( '.ml-tabnav-conditional' ).hide();
			tabContent.find( '.ml-tabnav-conditional.show-' + type ).show();
		}
	);

	jQuery( 'select.ml-select-taxonomy-ajax' ).on(
		'change',
		function() {
			var terms = jQuery( this ).parents('.tabnav-content').find( '.ml-select-terms-ajax' );
			var data = {
				action: 'ml_get_tax_terms',
				tax: jQuery( this ).val(),
				ml_nonce: jQuery( '#ml_nonce' ).val(),
			};
			jQuery.post(
				ajaxurl,
				data,
				function(response) {
					terms.html( response );
				}
			);
		}
	);

	jQuery( 'select.ml-tab-type' ).change();

	var _custom_media     = true,
		_orig_send_attachment = wp.media.editor.send.attachment;

	jQuery( '#ml_login_logo_upload_image_ios_button, #ml_login_logo_upload_image_android_button' ).click(
		function(e) {
			var send_attachment_bkp         = wp.media.editor.send.attachment;
			var button                      = jQuery( this );
			var id                          = button.attr( 'id' ).replace( '_button', '' );
			_custom_media                   = true;
			wp.media.editor.send.attachment = function(props, attachment) {
				if (_custom_media) {
					jQuery( "#" + id ).val( attachment.url );
				} else {
					return _orig_send_attachment.apply( this, [props, attachment] );
				}

				loadLoginPreviewImage( id );
			};

			wp.media.editor.open( button );
			return false;
		}
	);

	jQuery( "#ml-login-settings .ml-preview-image-remove-btn" ).click(
		function(e) {
			e.preventDefault();
			var confirmRemove = confirm( 'Are you sure you want to remove the image?' );
			if ( confirmRemove ) {
				jQuery( this ).parents( '.ml-col-half' ).find( ".ml-preview-upload-image-row" ).hide();
				jQuery( this ).parents( '.ml-col-half' ).find( ".ml-preview-image-holder img" ).attr( 'src', '' );
				jQuery( this ).parents( '.ml-col-half' ).find( ".image-selector" ).val( '' );
			}
		}
	);

	jQuery( '.dt-list-cb-toggle' ).on( 'click', function() {
		var toggleCb = jQuery( this );
		var toggleCbIsChecked = toggleCb.prop( 'checked' );
		var itemName = toggleCb.attr( 'name' );

		if ( toggleCbIsChecked ) {
			jQuery( '.' + itemName ).addClass( itemName + '--show' );
		} else {
			jQuery( '.' + itemName ).removeClass( itemName + '--show' );
		}
	} );

	const sectionTitle = jQuery( '.dt-admin__section-title' );

	/**
	 * Customization options.
	 * MobiLoud > Configuration > Design.
	 */
	sectionTitle.on( 'click', function() {
		const currentTitle = jQuery( this );
		const itemContent = currentTitle.next( '.dt-admin__item-content' );
		currentTitle.toggleClass( 'dt-admin__section-title--open' );
		itemContent.toggle( 300 );
	} );
});


var loadLoginPreviewImage = function( id ) {
	var input = jQuery( "#" + id );
	if ( input.val().length > 0 ) {
		input.parents( '.ml-col-half' ).find( ".ml-preview-upload-image-row" ).show();
		input.parents( '.ml-col-half' ).find( ".ml-preview-image-holder img" ).attr( 'src', input.val() );
	} else {
		input.parents( '.ml-col-half' ).find( ".ml-preview-upload-image-row" ).hide();
	}
};

var trim_string = function(string, length) {
	if(string.length <= length) {
		return string;
	} else {
		return string.substring(0, length) + '...';
	}
};