<?php
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( __FILE__ ) . '/api/compability.php';
	ml_compability_api_result( 'version' );
}

$info         = array( 'version' => '4.4.5' );
$use_callback = false;
if ( isset( $_GET['callback'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
	$callback = sanitize_text_field( wp_unslash( $_GET['callback'] ) ); // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
	if ( $callback && ( ! function_exists( 'wp_check_jsonp_callback' ) || wp_check_jsonp_callback( $callback ) ) ) {
		$use_callback = true;
		echo esc_js( $callback ) . '(';
	}
}
if ( strpos( $_SERVER['REQUEST_URI'], 'version' ) !== false ) { // phpcs:ignore WordPress.VIP.ValidatedSanitizedInput.InputNotValidated, WordPress.VIP.ValidatedSanitizedInput.MissingUnslash, WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
	echo wp_json_encode( $info );
}
if ( $use_callback ) {
	echo ')';
}
