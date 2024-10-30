jQuery(document).ready(function( $ ) {

	jQuery(".ml-iframe").on('load', function() {
		jQuery(".ml-loader").hide();
		jQuery(".ml-iframe").show();
	});

	const installPluginsBtn = $( '.ml-install-plugins-button' );
	const setupCommerceWrapper = $( '.ml-setup__commerce-wrapper' );
	const pluginInstallationStatus = $( '.ml-plugin-installation-status' );

	installPluginsBtn.on( 'click', function( e ) {
		e.preventDefault();

		const selectedPlugins = [];
		setupCommerceWrapper.find( '.ml-commerce-required-plugins-cb:checked' ).each( ( i, item ) => selectedPlugins.push( {
			slug: item.id,
			title: $( item ).closest( '.ml-setup__plugin-item-label' ).find( '.ml-setup__plugin-item-name' ).text(),
			entry: $( item ).attr( 'data-entry-file' ),
			exists: $( item ).attr( 'data-plugin-exists' ),
			active: $( item ).attr( 'data-plugin-active' ),
		} ) );

		if ( ! selectedPlugins.length ) {
			return;
		}

		let currentPlugin = 0;
		function setup_required_plugins( currentPlugin = 0 ) {
			if ( currentPlugin === selectedPlugins.length ) {
				pluginInstallationStatus.text( '' );
				return;
			}

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'ml_setup_install_plugins',
					plugin: selectedPlugins[ currentPlugin ],
					isLast: currentPlugin === selectedPlugins.length - 1,
				},
				beforeSend: function() {
					pluginInstallationStatus.text( `Installing ${ selectedPlugins[ currentPlugin ].title }...` );
				}
			} ).done( response => {
				if ( ! response.success ) {
					return;
				}

				if ( response.data && response.data.redirect_url ) {
					window.location.href = response.data.redirect_url;
				}

				setup_required_plugins( ++currentPlugin );
			} );
		}

		setup_required_plugins( currentPlugin );
	} );
});

( function( $ ) {
	$( document ).on( 'click', '.mlconf__live-preview-device-tab', function() {
		const devicesWrapper = $( '.mlconf__live-preview-device-wrapper' );
		const device = $( this ).data( 'live-preview-tab' );

		if ( ! $( this ).hasClass( 'ml-device-tab-selected' ) ) {
			$( this ).siblings().toggleClass( 'ml-device-tab-selected' );
			$( this ).toggleClass( 'ml-device-tab-selected' );
		}

		switch ( device ) {
			case 'ios':
				devicesWrapper.removeClass( 'ml-device--move-left' );
				devicesWrapper.addClass( 'ml-device--move-right' );
				break;
			case 'android':
				devicesWrapper.removeClass( 'ml-device--move-right' );
				devicesWrapper.addClass( 'ml-device--move-left' );
				break;

			default:
				break;
		}
	} );

	$( document ).on( 'click', '.sim-btn', function( e ) {
		console.log(e)
		e.preventDefault();
		const livePreviewWrapper = $( '#mlconf__live-preview-wrapper' );
		const devicesWrapperOverlay = $( '.mlconf__live-preview-overlay' );
		livePreviewWrapper.show();
		devicesWrapperOverlay.show();
	} );

	$( document ).on( 'click', '.mlconf__live-preview-wrapper--close', function() {
		const livePreviewWrapper = $( '#mlconf__live-preview-wrapper' );
		const devicesWrapperOverlay = $( '.mlconf__live-preview-overlay' );
		livePreviewWrapper.hide();
		devicesWrapperOverlay.hide();
	} );
} )( jQuery )
