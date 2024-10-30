<?php
ini_set( 'display_errors', 0 );

$subscription_endpoint = trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/subscription';

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
		}

		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_enqueue_scripts' );
		remove_all_actions( 'locale_stylesheet' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_shortlink_wp_head' );

		add_action( 'wp_print_styles', 'ml_sections_stylesheets' );

		add_action( 'wp_head', 'wp_print_styles' );

		wp_head();

		$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
		echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		?>
		<style type="text/css">
			<?php echo wp_kses_post( Mobiloud::get_option( 'ml_app_subscription_block_css' ) ); ?>
		</style>
	</head>
	<?php
		$platform = isset( $_SERVER['HTTP_X_ML_PLATFORM'] ) ? strtolower( wp_unslash( $_SERVER['HTTP_X_ML_PLATFORM'] ) ) : '';
	?>
	<body class="ml-subscription mb_body<?php echo esc_attr( " ml-platform-$platform" ); ?>">
		<div class="wrapper">
			<a id="ml-subscription-close" onclick="nativeFunctions.handleButton('close_screen', null, null)">+</a>
			<?php
			echo wp_kses( str_replace( '%LOGOURL%', Mobiloud::get_option( 'ml_preview_upload_image', '' ), Mobiloud::get_option( 'ml_app_subscription_block_content' ) ), Mobiloud::expanded_alowed_tags() );
			?>
		</div>
	</body>
</html>

