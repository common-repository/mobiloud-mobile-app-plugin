<?php

class Mobiloud_App_Preview {
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;

		add_action( 'wp_ajax_ml_preview_app_display', array( 'Mobiloud_App_Preview', 'render_preview' ) );
	}

	public static function render_preview() {
		if ( Mobiloud::is_action_allowed_ajax( 'tab_design' ) ) {
			self::process_data();

			Mobiloud_Admin::render_part_view( 'app_preview' );
			exit;
		}
	}

	public static function process_data() {
		// phpcs:disable ordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.VIP.SuperGlobalInputUsage.AccessDetected -- we already checked nonce before.
		if ( isset( $_POST['ml_preview_upload_image'] ) ) {
			if ( $_POST['ml_preview_upload_image'] != get_option( 'ml_preview_upload_image' ) ) {
				update_option( 'ml_preview_upload_image_time', time() );
			}
			update_option( 'ml_preview_upload_image', sanitize_text_field( $_POST['ml_preview_upload_image'] ) );
		}
		if ( isset( $_POST['ml_preview_theme_color'] ) ) {
			update_option( 'ml_preview_theme_color', sanitize_text_field( $_POST['ml_preview_theme_color'] ) );
		}
		if ( isset( $_POST['ml_preview_os'] ) ) {
			update_option( 'ml_preview_os', sanitize_text_field( $_POST['ml_preview_os'] ) );
		}
		if ( isset( $_POST['ml_article_list_view_type'] ) ) {
			Mobiloud::set_option( 'ml_article_list_view_type', sanitize_text_field( $_POST['ml_article_list_view_type'] ) );
		}

		if ( isset( $_POST['ml_datetype'] ) ) {
			Mobiloud::set_option( 'ml_datetype', sanitize_text_field( $_POST['ml_datetype'] ) );
		}

		if ( isset( $_POST['ml_dateformat'] ) ) {
			Mobiloud::set_option( 'ml_dateformat', sanitize_text_field( $_POST['ml_dateformat'] ) );
		}
		// phpcs:enable ordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.VIP.SuperGlobalInputUsage.AccessDetected
	}

	public static function get_preview_posts() {
		$args = array(
			'posts_per_page'   => 5,
			'offset'           => 0,
			'category'         => '',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		return get_posts( $args );
	}

	public static function get_color_brightness( $hex ) {
		// returns brightness value from 0 to 255.
		// strip off any leading #.
		$hex = str_replace( '#', '', $hex );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		return ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;
	}

}
