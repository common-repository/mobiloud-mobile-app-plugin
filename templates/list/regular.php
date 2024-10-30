<?php
/**
 * This is a list template: regular.php.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/list
 * @version 4.2.0
 */

?>
<!DOCTYPE html>
<html dir="<?php echo( get_option( 'ml_rtl_text_enable' ) ? 'rtl' : 'ltr' ); ?>">
	<head>
		<?php
		/**
		 * @var string $list_slug defined in parent template loop.php
		 */
		// include header part.
		$_names   = [ "{$list_type}_head{$list_slug}", "{$list_type}_head", 'regular_head' ];
		$template = Mobiloud::use_template( 'list', $_names, false );
		if ( '' !== $template ) {
			require $template;
		}

		echo wp_kses(stripslashes(get_option('ml_html_home_list_head', '')), Mobiloud::expanded_alowed_tags());

		/**
		* Called before </head> at the loop page.
		*
		* @since 4.2.0
		*/
		do_action( 'mobiloud_list_end_of_head' );
		?>
	</head>

	<?php
	// *********************************************************
	$body_classes = array( 'ml-article-list', "ml-list-{$list_type}", Mobiloud::get_template_class( __FILE__, 'ml-list-' ) );
	if ( '' !== $list_slug ) {
		$body_classes[] = "ml-list-{$list_type}{$list_slug}";
	}
	// Compact or extended theme.
	$body_classes[] = 'ml-theme-' . get_option( 'ml_article_list_view_type', 'compact' ); // 'ml-theme-compact', 'ml-theme-extended'.
	// Add request parameters to body classes list.
	if ( ! empty( $_GET ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.VIP.SuperGlobalInputUsage.AccessDetected -- no need in nonce here.
		foreach ( $_GET as $name => $value ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.VIP.SuperGlobalInputUsage.AccessDetected -- no need in nonce here.
			$body_classes[] = sanitize_html_class( 'ml-arg-' . strtolower( str_replace( ',', '_', sanitize_text_field( $name ) . '--' . sanitize_text_field( $value ) ) ) );
		}
	}
	// Backward compability: those body classes used at versions before 4.2.0.
	if ( 'search' === $list_type ) {
		$body_classes[] = 'ml-search-results';
	} elseif ( 'favorites' === $list_type ) {
		$body_classes[] = 'ml-favorites-list';
	} elseif ( 'custom' === $list_type ) {
		$body_classes[] = 'ml-custom-type';
	} else {
		$body_classes[] = 'ml-regular-list';
	}

	/**
	* Modify body classes list for the page.
	*
	* @since 4.2.0
	*
	* @param string[] $body_classes  Array with class names.
	* @param string   $template_type Template type where it called: 'list', 'comments', etc.
	*/
	$body_classes = apply_filters( 'mobiloud_body_class', $body_classes, 'list' );
	?>
	<body class="<?php echo esc_attr( implode( ' ', $body_classes ) ); ?>">
		<?php
		do_action( 'mobiloud_before_content_requests' );

		// include body part.
		$_names   = [ "{$list_type}_body{$list_slug}", "{$list_type}_body", 'regular_body' ];
		$template = Mobiloud::use_template( 'list', $_names, false );
		if ( '' !== $template ) {
			require $template;
		}

		// include footer part.
		$_names   = [ "{$list_type}_footer{$list_slug}", "{$list_type}_footer", 'regular_footer' ];
		$template = Mobiloud::use_template( 'list', $_names, false );
		if ( '' !== $template ) {
			require $template;
		}

		/**
		* Called before </head>, after inline js block.
		* Embed any custom JS using this action.
		*/
		do_action( 'mobiloud_custom_list_scripts' );
		eval( stripslashes( get_option( 'ml_post_footer' ) ) );
		?>
	</body>
</html>
