<?php
error_reporting( E_ALL );
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( __FILE__ ) . '/api/compability.php';
	ml_compability_api_result( 'config' );
}

require_once 'categories.php';

require_once dirname( __FILE__ ) . '/class.mobiloud-app-preview.php';
require_once dirname( __FILE__ ) . '/subscriptions/functions.php';

$return_config                                 = array();
$return_config['app_name']                     = get_option( 'ml_app_name', '' );
$return_config['enable_featured_images']       = get_option( 'ml_show_article_featuredimage', true );
$return_config['enable_dates']                 = get_option( 'ml_article_list_enable_dates', true );
$return_config['show-android-cat-tabs']        = get_option( 'ml_show_android_cat_tabs', false );
$return_config['allow_landscape']              = get_option( 'ml_allow_landscape', false );
$return_config['google-tracking-id']           = get_option( 'ml_google_tracking_id', '' );
$return_config['facebook_appid']               = get_option( 'ml_fb_app_id', '' );
$return_config['quantcast_api_key']            = get_option( 'ml_qm_api_key', '' );
$return_config['comscore_c2']                  = get_option( 'ml_comscore_c2', '' );
$return_config['comscore_secret']              = get_option( 'ml_comscore_secret', '' );
$return_config['show_custom_field']            = get_option( 'ml_custom_field_enable', false );
$return_config['show_excerpts']                = get_option( 'ml_article_list_show_excerpt', false );
$return_config['show_comments_count']          = get_option( 'ml_article_list_show_comment_count', false );
$return_config['copyright_string']             = html_entity_decode( get_option( 'ml_copyright_string', '' ) );
$return_config['list_format']                  = get_option( 'ml_article_list_view_type', 'extended' );
$return_config['comments_system']              = get_option( 'ml_comments_system', 'wordpress' ); // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
$return_config['disqus_shortname']             = get_option( 'ml_disqus_shortname', '' );
$return_config['show_contact_email']           = get_option( 'ml_show_email_contact_link', false );
$return_config['contact_email']                = get_option( 'ml_contact_link_email', '' );
$return_config['timezone']                     = strval( get_option( 'gmt_offset' ) );
$return_config['datetype']                     = get_option( 'ml_datetype', 'prettydate' );
$return_config['dateformat']                   = get_option( 'ml_dateformat', 'F j, Y' );
$return_config['original_size_image_list']     = get_option( 'ml_original_size_image_list', true );
$return_config['original_size_featured_image'] = get_option( 'ml_original_size_featured_image', true );

$return_config['custom_featured_image'] = get_option( 'ml_custom_featured_image', '' );

if ( ( get_option( 'ml_home_article_list_enabled', false ) == true )
|| ( get_option( 'ml_home_page_enabled', false ) == true ) && ! ( get_option( 'ml_home_page_id', false ) ) // also show if "Page contents" is active, but no page selected
 || ( get_option( 'ml_home_url_enabled', false ) == true ) && ! ( get_option( 'ml_home_url', false ) ) ) { // or "URL" is active, but empty
	$return_config['home_page_type'] = 'article_list';
} elseif ( get_option( 'ml_home_page_enabled', false ) == true ) {

	$return_config['home_page_type'] = 'page';
	$return_config['home_page_id']   = get_option( 'ml_home_page_id' );
	$return_config['home_page_full'] = get_option( 'ml_home_page_full' );

	$return_config['show_article_list_menu_item']  = get_option( 'ml_show_article_list_menu_item', false );
	$return_config['article_list_menu_item_title'] = get_option( 'ml_article_list_menu_item_title', 'Articles' );


} elseif ( get_option( 'ml_home_url_enabled', false ) == true ) {
	$return_config['home_page_type'] = 'url';
	$return_config['home_page_url']  = get_option( 'ml_home_url' );

	$return_config['show_article_list_menu_item']  = get_option( 'ml_show_article_list_menu_item', true );
	$return_config['article_list_menu_item_title'] = get_option( 'ml_article_list_menu_item_title', 'Articles' );

}

// advertising
$return_config['privacy_policy_url']   = Mobiloud::get_option( 'ml_privacy_policy_url' );
$return_config['advertising_platform'] = Mobiloud::get_option( 'ml_advertising_platform' );

$return_config['ios_admob_app_id']               = Mobiloud::get_option( 'ml_ios_admob_app_id' );
$return_config['ios_phone_banner_unit_id']       = Mobiloud::get_option( 'ml_ios_phone_banner_unit_id' );
$return_config['ios_tablet_banner_unit_id']      = Mobiloud::get_option( 'ml_ios_tablet_banner_unit_id' );
$return_config['ios_banner_position']            = Mobiloud::get_option( 'ml_ios_banner_position' );
$return_config['ios_interstitial_unit_id']       = Mobiloud::get_option( 'ml_ios_interstitial_unit_id' );
$return_config['ios_interstitial_interval']      = Mobiloud::get_option( 'ml_ios_interstitial_interval' );
$return_config['ios_native_ad_unit_id']          = Mobiloud::get_option( 'ml_ios_native_ad_unit_id' );
$return_config['ios_native_ad_interval']         = Mobiloud::get_option( 'ml_ios_native_ad_interval' );
$return_config['ios_native_ad_type']             = Mobiloud::get_option( 'ml_ios_native_ad_type', 'medium' );
$return_config['ios_native_ad_article_unit_id']  = Mobiloud::get_option( 'ml_ios_native_ad_article_unit_id' );
$return_config['ios_native_ad_article_position'] = Mobiloud::get_option( 'ml_ios_native_ad_article_position', 'both' );
$return_config['ios_native_ad_article_type']     = Mobiloud::get_option( 'ml_ios_native_ad_article_type', 'medium' );

$return_config['android_admob_app_id']               = Mobiloud::get_option( 'ml_android_admob_app_id' );
$return_config['android_phone_banner_unit_id']       = Mobiloud::get_option( 'ml_android_phone_banner_unit_id' );
$return_config['android_tablet_banner_unit_id']      = Mobiloud::get_option( 'ml_android_tablet_banner_unit_id' );
$return_config['android_banner_position']            = Mobiloud::get_option( 'ml_android_banner_position' );
$return_config['android_interstitial_unit_id']       = Mobiloud::get_option( 'ml_android_interstitial_unit_id' );
$return_config['android_interstitial_interval']      = Mobiloud::get_option( 'ml_android_interstitial_interval' );
$return_config['android_native_ad_unit_id']          = Mobiloud::get_option( 'ml_android_native_ad_unit_id' );
$return_config['android_native_ad_interval']         = Mobiloud::get_option( 'ml_android_native_ad_interval' );
$return_config['android_native_ad_type']             = Mobiloud::get_option( 'ml_android_native_ad_type', 'medium' );
$return_config['android_native_ad_article_unit_id']  = Mobiloud::get_option( 'ml_android_native_ad_article_unit_id' );
$return_config['android_native_ad_article_position'] = Mobiloud::get_option( 'ml_android_native_ad_article_position', 'both' );
$return_config['android_native_ad_article_type']     = Mobiloud::get_option( 'ml_android_native_ad_article_type', 'medium' );

$return_config['enable_hierarchical_pages'] = get_option( 'ml_hierarchical_pages_enabled', false );
$return_config['show_favorites']            = get_option( 'ml_menu_show_favorites', true );
$return_config['followimagelinks']          = get_option( 'ml_followimagelinks', 1 );

$return_config['interface_images_updated'] = date( 'c', get_option( 'ml_preview_upload_image_time' ) );
$return_config['interface_images']         = array(
	'navigation_bar_logo' => get_option( 'ml_preview_upload_image' ),
);

$navigation_bar_text = '#000000';
if ( Mobiloud_App_Preview::get_color_brightness( get_option( 'ml_preview_theme_color' ) ) < 190 ) {
	$navigation_bar_text = '#FFFFFF';
}
$return_config['interface_colors']    = array(
	'navigation_bar_background'  => get_option( 'ml_preview_theme_color' ),
	'navigation_bar_text'        => $navigation_bar_text,
	'navigation_bar_button_text' => $navigation_bar_text,
);
$return_config['image_cache_preload'] = get_option( 'ml_image_cache_preload', false );

$return_config['show_rating_prompt']          = get_option( 'ml_show_rating_prompt', false );
$return_config['days_interval_rating_prompt'] = get_option( 'ml_days_interval_rating_prompt', 1 );

$return_config['welcome_screen_url']              = get_option( 'ml_welcome_screen_url', '' );
$return_config['welcome_screen_required_version'] = get_option( 'ml_welcome_screen_required_version', '1.0' );

$return_config['rtl_text_enable'] = get_option( 'ml_rtl_text_enable', false );

$return_config['related_posts']   = get_option( 'ml_related_posts', false );
$return_config['related_header']  = get_option( 'ml_related_header', '' );
$return_config['related_image']   = get_option( 'ml_related_image', false );
$return_config['related_excerpt'] = get_option( 'ml_related_excerpt', false );
$return_config['related_date']    = get_option( 'ml_related_date', false );

$return_config['app_subscription_enabled']                        = get_option( 'ml_app_subscription_enabled', false );
$return_config['app_subscription_ios_in_app_purchase_id']         = get_option( 'ml_app_subscription_ios_in_app_purchase_id', '' );
$return_config['app_subscription_android_in_app_purchase_id']     = get_option( 'ml_app_subscription_android_in_app_purchase_id', '' );
$return_config['app_subscriptions_subscribe_link_text']           = get_option( 'ml_app_subscriptions_subscribe_link_text', '' );
$return_config['app_subscriptions_manage_subscription_link_text'] = get_option( 'ml_app_subscriptions_manage_subscription_link_text', '' );

$return_config['ios_phone_banner_app_subscription_show']          = get_option( 'ml_ios_phone_banner_app_subscription_show', true );
$return_config['android_phone_banner_app_subscription_show']      = get_option( 'ml_android_phone_banner_app_subscription_show', true );
$return_config['ios_tablet_banner_app_subscription_show']         = get_option( 'ml_ios_tablet_banner_app_subscription_show', true );
$return_config['android_tablet_banner_app_subscription_show']     = get_option( 'ml_android_tablet_app_subscription_show', true );
$return_config['ios_interstitial_app_subscription_show']          = get_option( 'ml_ios_interstitial_app_subscription_show', true );
$return_config['android_interstitial_app_subscription_show']      = get_option( 'ml_android_interstitial_app_subscription_show', true );
$return_config['ios_native_ad_app_subscription_show']             = get_option( 'ml_ios_native_ad_app_subscription_show', true );
$return_config['android_native_ad_app_subscription_show']         = get_option( 'ml_android_native_ad_app_subscription_show', true );
$return_config['ios_native_ad_article_app_subscription_show']     = get_option( 'ml_ios_native_ad_article_app_subscription_show', true );
$return_config['android_native_ad_article_app_subscription_show'] = get_option( 'ml_android_native_ad_article_app_subscription_show', true );
$return_config['banner_above_content_app_subscription_show']      = get_option( 'ml_banner_above_content_app_subscription_show', true ) ? '1' : '';
$return_config['banner_above_title_app_subscription_show']        = get_option( 'ml_banner_above_title_app_subscription_show', true ) ? '1' : '';
$return_config['banner_below_content_app_subscription_show']      = get_option( 'ml_banner_below_content_app_subscription_show', true ) ? '1' : '';

// Article List type.
$return_config['list_type'] = 'web';

/**
 * Add custom menu fields to menu
 *
 * @param mixed $menu_item
 */
function ml_menu_add_custom_nav_fields( $menu_item ) {

	$menu_item->opening_method = get_post_meta( $menu_item->ID, '_ml_menu_item_opening_method', true );
	return $menu_item;

}
add_filter( 'wp_setup_nav_menu_item', 'ml_menu_add_custom_nav_fields' );

function ml_get_menu_config( $menu_slug ) {
	$menu  = wp_get_nav_menu_items( $menu_slug );
	$items = array();

	if ( $menu ) {
		foreach ( $menu as $menu_item ) {
			if ( $menu_item->menu_item_parent !== '0' ) {
				continue;
			} else {

				$list_type = 'web';

				if ( $list_type == 'web' ) {
					$item_endpoint_url = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/list';
				} else {
					$item_endpoint_url = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/posts';
				}

				$is_custom_tax = false;

				switch ( $menu_item->type ) {
					case 'custom':
						$ml_horizontal_item_type = 'custom_link';
						$item_endpoint_url       = '';
						break;

					case 'post_type':
						$ml_horizontal_item_type = $menu_item->object;

						if ( 'list-builder' === $menu_item->object ) {
							$item_endpoint_url = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/listbuilder/' . $menu_item->object_id;
						} else {
							$item_endpoint_url = '';
						}
						break;

					case 'taxonomy':
						$ml_horizontal_item_type = 'category';
						$item_endpoint_url      .= '?taxonomy=' . $menu_item->object . '&term_id=' . $menu_item->object_id . '&order=DESC&orderby=date';
						$is_custom_tax           = true;
						break;

					case 'favorites':
						$ml_horizontal_item_type = 'favorites';
						$item_endpoint_url       = '';
						break;

					case 'settings':
						$ml_horizontal_item_type = 'settings';
						$item_endpoint_url       = '';
						break;

					case 'home_screen':
						$ml_horizontal_item_type = 'home_screen';
						$item_endpoint_url       = '';
						break;

					case 'login':
						$ml_horizontal_item_type = 'login';
						$item_endpoint_url       = '';
						break;

					case 'registration':
						$ml_horizontal_item_type = 'registration';
						$item_endpoint_url       = '';
						break;

					default:
						$ml_horizontal_item_type = $menu_item->object;
						$item_endpoint_url       = '';
						break;
				}

				$item_data = array(
					'label'          => html_entity_decode( $menu_item->title ),
					'type'           => $ml_horizontal_item_type,
					'link'           => $menu_item->url,
					'id'             => $menu_item->object_id,
					'endpoint_url'   => $item_endpoint_url,
					'opening_method' => ( ! empty( $menu_item->opening_method ) ) ? $menu_item->opening_method : 'native',
				);

				if ( $ml_horizontal_item_type == 'category' && ! $is_custom_tax ) {
					$cat               = get_category( $menu_item->object_id );
					$item_data['slug'] = $cat->slug;
				}

				$items[] = apply_filters( 'ml_get_menu_config_item', $item_data );
			}
		}
	}

	return apply_filters( 'ml_get_menu_config', $items );
}

$ml_horizontal_nav = Mobiloud::get_option( 'ml_horizontal_nav' );
if ( empty( $ml_horizontal_nav ) ) {
	$return_config['horizontal_navigation_enabled'] = '0';
} else {
	$return_config['horizontal_navigation_enabled'] = '1';
	$return_config['horizontal_navigation']         = ml_get_menu_config( $ml_horizontal_nav );
}

$ml_hamburger_nav = Mobiloud::get_option( 'ml_hamburger_nav' );
if ( empty( $ml_hamburger_nav ) ) {
	$return_config['hamburger_navigation_enabled'] = '0';
} else {
	$return_config['hamburger_navigation_enabled'] = '1';
	$return_config['hamburger_navigation']         = ml_get_menu_config( $ml_hamburger_nav );
}

$ml_tabbed_navigation_enabled = Mobiloud::get_option( 'ml_tabbed_navigation_enabled' );
if ( empty( $ml_tabbed_navigation_enabled ) ) {
	$return_config['tabbed_navigation_enabled'] = '0';
} else {
	$return_config['tabbed_navigation_enabled'] = '1';

	$ml_tabbed_navigation = Mobiloud::get_option( 'ml_tabbed_navigation' );

	foreach ( $ml_tabbed_navigation['tabs'] as $i => $tab ) {
		// choose correct endpoint.
		if ( 'web' === $return_config['list_type'] && isset( $tab['endpoint_url_web'] ) ) {
			$ml_tabbed_navigation['tabs'][ $i ]['endpoint_url'] = $ml_tabbed_navigation['tabs'][ $i ]['endpoint_url_web'];
		}
		unset( $ml_tabbed_navigation['tabs'][ $i ]['endpoint_url_web'] );
		// fill menu.
		if ( ! empty( $tab['horizontal_navigation'] ) ) {
			$ml_tabbed_navigation['tabs'][ $i ]['horizontal_navigation'] = ml_get_menu_config( $tab['horizontal_navigation'] );

		} else {
			$ml_tabbed_navigation['tabs'][ $i ]['horizontal_navigation'] = array();
		}

		// Change 'type' to 'link' if 'list'.
		if ( isset( $ml_tabbed_navigation['tabs'][ $i ]['type'] ) && 'list' === $ml_tabbed_navigation['tabs'][ $i ]['type'] ) {
			if ( isset( $ml_tabbed_navigation['tabs'][ $i ]['list'] ) ) {
				$ml_tabbed_navigation['tabs'][ $i ]['type'] = 'link';
				$ml_tabbed_navigation['tabs'][ $i ]['url'] = sprintf(
					"%s/%s/%s",
					get_site_url(),
					'ml-api/v2/listbuilder',
					$ml_tabbed_navigation['tabs'][ $i ]['list']
				);
			}
		}
	}

	$return_config['tabbed_navigation'] = $ml_tabbed_navigation;
}

// Login settings.
$default_login_settings          = array(
	'ios_login_type'     => 'disabled',
	'android_login_type' => 'disabled',
);
$return_config['login_settings'] = get_option( 'ml_login_settings', $default_login_settings );

// Settings page.
$return_config['ml_share_app_url']                   = Mobiloud::get_option( 'ml_share_app_url' );
$return_config['push_notification_settings_enabled'] = Mobiloud::get_option( 'ml_push_notification_settings_enabled' );
$ml_push_notification_menu                           = Mobiloud::get_option( 'ml_push_notification_menu' );
$menu  = wp_get_nav_menu_items( $ml_push_notification_menu );
$items = array();

if ( $menu ) {
	foreach ( $menu as $menu_item ) {
		if ( $menu_item->menu_item_parent !== '0' ) {
			continue;
		} else {
			if ( 'custom' === $menu_item->type ) {
				$item = array(
					'label' => $menu_item->title,
					'tag'   => sanitize_title( $menu_item->title ),
				);
			} else {
				$item = array(
					'label' => html_entity_decode( $menu_item->title ),
					'tag'   => $menu_item->object_id,
				);
			}
			$items[] = $item;
		}
	}
}
$return_config['push_notification_settings'] = $items;

$return_config['general_settings_enabled'] = Mobiloud::get_option( 'ml_general_settings_enabled' );
$ml_general_settings_menu                  = Mobiloud::get_option( 'ml_general_settings_menu' );
$menu                                      = wp_get_nav_menu_items( $ml_general_settings_menu );
$items                                     = array();

if ( $menu ) {
	foreach ( $menu as $menu_item ) {
		if ( $menu_item->menu_item_parent !== '0' || $menu_item->type != 'custom' ) {
			continue;
		} else {
			$items[] = array(
				'label' => $menu_item->title,
				'url'   => $menu_item->url,
			);
		}
	}
}
$return_config['general_settings'] = ml_get_menu_config( $ml_general_settings_menu );

$return_config['settings_title_color']                      = Mobiloud::get_option( 'ml_settings_title_color' );
$return_config['settings_active_switch_color']              = Mobiloud::get_option( 'ml_settings_active_switch_color' );
$return_config['settings_active_switch_background_color']   = Mobiloud::get_option( 'ml_settings_active_switch_background_color' );
$return_config['settings_inactive_switch_color']            = Mobiloud::get_option( 'ml_settings_inactive_switch_color' );
$return_config['settings_inactive_switch_background_color'] = Mobiloud::get_option( 'ml_settings_inactive_switch_background_color' );
$return_config['caching_enabled']                           = Mobiloud::get_option( 'ml_caching_enabled' );

$return_config['cache_busting_enabled']  = (string) get_option( 'ml_cache_busting_enabled', false );
$return_config['cache_busting_interval'] = get_option( 'ml_cache_busting_interval', '15' );

$return_config['dark_mode_enabled']                              = (string) get_option( 'ml_dark_mode_enabled', false );
$return_config['dark_mode_logo']                                 = (string) get_option( 'ml_dark_mode_logo', '' );
$return_config['dark_mode_header_color']                         = (string) get_option( 'ml_dark_mode_header_color', '' );
$return_config['dark_mode_tabbed_navigation_color']              = (string) get_option( 'ml_dark_mode_tabbed_navigation_color', '' );
$return_config['dark_mode_tabbed_navigation_icons_color']        = (string) get_option( 'ml_dark_mode_tabbed_navigation_icons_color', '' );
$return_config['dark_mode_tabbed_navigation_active_icon_color']  = (string) get_option( 'ml_dark_mode_tabbed_navigation_active_icon_color', '' );
$return_config['dark_mode_notification_switch_main_color']       = (string) get_option( 'ml_dark_mode_notification_switch_main_color', '' );
$return_config['dark_mode_notification_switch_background_color'] = (string) get_option( 'ml_dark_mode_notification_switch_background_color', '' );
$return_config['dark_mode_hamburger_menu_background_color']      = (string) get_option( 'ml_dark_mode_hamburger_menu_background_color', '' );
$return_config['dark_mode_hamburger_menu_text_color']            = (string) get_option( 'ml_dark_mode_hamburger_menu_text_color', '' );


$return_config['dark_mode_custom_css'] = (string) get_option( 'ml_dark_mode_custom_css', '' );
$return_config['pull_latest_content_enabled'] = (string) get_option( 'ml_always_pull_post', '' );

if ( ! empty( $return_config ) ) {
	foreach ( $return_config as $key => $val ) {
		if ( $val === null ) {
			$return_config[ $key ] = '';
		}
	}
}

echo wp_json_encode( apply_filters( 'mobiloud_config_endpoint', $return_config ) );

function filter_api( $url ) {
	return apply_filter( 'mobiloud_api_url', $url );
}
