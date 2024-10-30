<?php
/**
 * This is a registration template: form.php.
 *
 * It choose and include one of specialized templates (search, favorites, custom-(parameter), custom or regular) using request parameters.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/registration
 * @version 4.2.0
 */

ini_set( 'display_errors', 0 );

$subscription_endpoint = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/subscription';
$receipt_id            = base64_encode( isset( $_SERVER['HTTP_X_ML_RECEIPT_ID'] ) ? $_SERVER['HTTP_X_ML_RECEIPT_ID'] : '' );
?>
<!DOCTYPE html>
<html dir="<?php echo( get_option( 'ml_rtl_text_enable' ) ? 'rtl' : 'ltr' ); ?>">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="language" content="en"/>
		<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

		<?php

		function ml_sections_stylesheets() {
			wp_enqueue_style( 'mobiloud-post', MOBILOUD_PLUGIN_URL . 'post/css/styles.css' );
			wp_enqueue_style( 'mobiloud-typeplate', MOBILOUD_PLUGIN_URL . 'post/css/_typeplate.css' );

			wp_register_script( 'mobiloud-registration', MOBILOUD_PLUGIN_URL . 'assets/js/registration.js', [ 'jquery' ], MOBILOUD_PLUGIN_VERSION );
			wp_localize_script(
				'mobiloud-registration', 'ml_registration', [
					'endpoint'         => trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/registration/data',
					'username_empty'   => __( 'Please provide an email address', 'mobiloud' ),
					'password_empty'   => __( 'Please provide a password.', 'mobiloud' ),
					'terms_empty'      => __( 'Please accept the terms of agreement.', 'mobiloud' ),
					'unexpected_error' => __( 'Unexpected error. Please try again later.', 'mobiloud' ),
				]
			);

			wp_enqueue_script( 'mobiloud-registration' );
		}

		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_enqueue_scripts' );
		remove_all_actions( 'locale_stylesheet' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_shortlink_wp_head' );

		add_action( 'wp_print_styles', 'ml_sections_stylesheets' );
		add_action( 'wp_head', 'wp_print_styles' );
		add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 300 );

		wp_head();

		$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
		echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		?>
		<style type="text/css">
			<?php echo wp_kses_post( str_replace( '%LOGOURL%', Mobiloud::get_option( 'ml_preview_upload_image', '' ), Mobiloud::get_option( 'ml_app_registration_block_css', '' ) ) ); ?>
		</style>
	</head>
	<?php
	$platform = isset( $_SERVER['HTTP_X_ML_PLATFORM'] ) ? strtolower( wp_unslash( $_SERVER['HTTP_X_ML_PLATFORM'] ) ) : '';
	?>
	<body class="ml-registration mb_body<?php echo esc_attr( " ml-platform-$platform" ); ?>">
		<div class="ml-loader"></div>
		<div class="wrapper">
			<a class="ml-close" onclick="nativeFunctions.handleButton('close_screen', null, null)">+</a>
			<div class="reg-content" id="reg_content">
				<?php
				if ( '' !== $receipt_id ) {
					$content = Mobiloud::get_option( 'ml_app_registration_block_content' );
					echo wp_kses( str_replace( '%LOGOURL%', Mobiloud::get_option( 'ml_preview_upload_image', '' ), $content ), Mobiloud::expanded_alowed_tags() );
					?>
				</div>
				<input type="hidden" id="reg_receipt" value="<?php echo esc_attr( $receipt_id ); ?>">
					<?php wp_nonce_field( 'ml-reg', 'reg_nonce' ); ?>
					<?php
					do_action( 'wp_print_footer_scripts' );
				} else {
					$email = Mobiloud::get_option( 'ml_contact_link_email', get_bloginfo( 'admin_email' ) );
					?>
				<div class="registration-errors" id="reg_errors" style="display: block;">
					<p>There was a problem processing your subscription, please contact <a href="<?php echo esc_attr( 'mailto:' . $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
				</div>
					<?php
				}
				?>
			<div class="reg-success" id="reg_success"></div>
		</div>
	</body>
</html>
