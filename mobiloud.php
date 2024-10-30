<?php
/*
Plugin Name: MobiLoud
Plugin URI: https://www.mobiloud.com
Description: Easily publish native mobile apps for your WordPress site.
Author: MobiLoud
Version: 4.6.1
Author URI: https://www.mobiloud.com
*/

global $wp_version;
define( 'MOBILOUD_PLUGIN_VERSION', '4.6.1' );
define( 'MOBILOUD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MOBILOUD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MOBILOUD_POST_ASSETS_URL', MOBILOUD_PLUGIN_URL . '/post' );
define( 'MOBILOUD_CANVAS_WEBSITE_URL', 'https://canvas.mobiloud.com' );

require_once MOBILOUD_PLUGIN_DIR . 'push.php';
require_once MOBILOUD_PLUGIN_DIR . 'subscriptions/functions.php';
require_once MOBILOUD_PLUGIN_DIR . 'push_notifications/menu.php';
require_once MOBILOUD_PLUGIN_DIR . 'push_notifications/utilities.php';
require_once MOBILOUD_PLUGIN_DIR . 'api/MLAPI.php';
require_once MOBILOUD_PLUGIN_DIR . 'api/MLRestAPI.php';

require_once MOBILOUD_PLUGIN_DIR . 'class.mobiloud-app-preview.php';
require_once MOBILOUD_PLUGIN_DIR . 'class.mobiloud-cache.php';
require_once MOBILOUD_PLUGIN_DIR . 'class.mobiloud.php';
require_once MOBILOUD_PLUGIN_DIR . '3rd-party-plugins.php';
require_once MOBILOUD_PLUGIN_DIR . 'class.mobiloud-woo-order.php';

register_activation_hook( __FILE__, array( 'Mobiloud', 'mobiloud_activate' ) );
register_uninstall_hook( __FILE__, array( 'Mobiloud', 'uninstall' ) );
add_action( 'init', array( 'Mobiloud', 'init' ) );
if ( is_admin() || wp_doing_cron() ) {
	require_once MOBILOUD_PLUGIN_DIR . 'class.mobiloud-admin.php';
	add_action( 'init', array( 'Mobiloud_Admin', 'init' ) );

	if ( version_compare( $wp_version, '5.4', '<' ) ) {
		require_once MOBILOUD_PLUGIN_DIR . 'class.mobiloud-admin-menu-walker.php';
	}
}

add_action( 'init', function () {
	$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
	$step = filter_input( INPUT_GET, 'step', FILTER_SANITIZE_STRING );

	if ( 'mobiloud' === $page && 'welcome-close' === $step ) {
		update_option( 'ml_activated', true );
	}
}, 5, 1 );

require_once MOBILOUD_PLUGIN_DIR . 'blocks/class-setup.php';
require_once MOBILOUD_PLUGIN_DIR . 'post-types/list-builder.php';
require_once MOBILOUD_PLUGIN_DIR . 'post-types/app-pages.php';
require_once MOBILOUD_PLUGIN_DIR . 'blocks/blocks.php';

$restapi = new MLRestAPI();
add_action( 'init', 'mobiloud_plugin_init' );
add_action( 'plugins_loaded', [ 'MLAPI', 'plugins_loaded' ] );

// INIT.
function mobiloud_plugin_init() {
	global $ml_api_key, $ml_secret_key, $ml_server_host, $ml_server_port;
	global $ml_pb_app_id, $ml_pb_secret_key;
	global $ml_last_post_id;

	global $ml_push_url;

	// variabili che servono a verificare quando un certificato e` stato inviato correttamente.
	global $ml_has_prod_cert, $ml_has_dev_cert;

	global $mobiloud_charts_url;

	// mobile promotional message.
	global $ml_popup_message_on_mobile_active, $ml_popup_message_on_mobile_url;

	// general configuration.
	global $ml_automatic_image_resize;
	global $ml_push_notification_enabled;
	global $ml_html_banners_enable;

	global $ml_article_list_enable_dates;
	global $ml_article_list_enable_featured_images;

	global $ml_home_article_list_enabled;
	global $ml_home_page_enabled;
	global $ml_home_url_enabled;

	global $ml_home_page_full;
	global $ml_home_page_id;
	global $ml_home_url;

	global $ml_show_article_list_menu_item;
	global $ml_article_list_menu_item_title;

	$ml_home_article_list_enabled = get_option( 'ml_home_article_list_enabled', true );
	$ml_home_page_enabled         = get_option( 'ml_home_page_enabled', false );
	$ml_home_url_enabled          = get_option( 'ml_home_url_enabled', false );

	$ml_home_page_full = get_option( 'ml_home_page_full', false );
	$ml_home_page_id   = get_option( 'ml_home_page_id' );
	$ml_home_url       = get_option( 'ml_home_url' );

	$ml_show_article_list_menu_item  = get_option( 'ml_show_article_list_menu_item', true );
	$ml_article_list_menu_item_title = get_option( 'ml_article_list_menu_item_title', 'Articles' );

	global $ml_hierarchical_pages_enabled;
	$ml_hierarchical_pages_enabled = get_option( 'ml_hierarchical_pages_enabled', true );

	global $ml_article_list_include_post_types;
	$ml_article_list_include_post_types = get_option( 'ml_article_list_include_post_types', 'post' );

	global $ml_article_list_exclude_categories;
	$ml_article_list_exclude_categories = get_option( 'ml_article_list_exclude_categories', '' );

	global $ml_include_pages_in_search;
	$ml_include_pages_in_search = get_option( 'ml_include_pages_in_search', false );

	global $ml_menu_show_favorites;
	$ml_menu_show_favorites = get_option( 'ml_menu_show_favorites', true );

	global $ml_menu_urls;
	$ml_menu_urls = get_option( 'ml_menu_urls', array() );

	// content redirect.
	global $ml_content_redirect_enable;
	global $ml_content_redirect_url;
	global $ml_content_redirect_category;

	$ml_html_banners_enable                 = get_option( 'ml_html_banners_enable' );
	$ml_article_list_enable_dates           = get_option( 'ml_article_list_enable_dates', true );
	$ml_article_list_enable_featured_images = get_option( 'ml_article_list_enable_featured_images', true );

	$ml_pb_app_id     = get_option( 'ml_pb_app_id' );
	$ml_pb_secret_key = get_option( 'ml_pb_secret_key' );

	$ml_last_post_id = get_option( 'ml_last_post_id' );

	$ml_fb_app_id     = get_option( 'ml_fb_app_id' );
	$ml_fb_secret_key = get_option( 'ml_fb_secret_key' );

	$ml_popup_message_on_mobile_active = get_option( 'ml_popup_message_on_mobile_active' );
	$ml_popup_message_on_mobile_appid  = get_option( 'ml_popup_message_on_mobile_appid' );

	if ( ! class_exists( 'WP_Http' ) ) {
		include_once ABSPATH . WPINC . '/class-http.php';
	}

	// push notifications.
	$ml_push_notification_enabled = get_option( 'ml_push_notification_enabled' );

	// content redirect.
	$ml_content_redirect_enable = get_option( 'ml_content_redirect_enable' );
	$ml_content_redirect_url    = get_option( 'ml_content_redirect_url' );
	$ml_content_redirect_slug   = get_option( 'ml_content_redirect_slug' );

}
