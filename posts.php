<?php
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( __FILE__ ) . '/api/compability.php';
	ml_compability_api_result( 'posts' );
}

$debug = true;

remove_all_actions( 'wp_login_failed' );
remove_all_actions( 'authenticate' );

// do_action( 'mobiloud_before_content_requests' );
header( 'Content-type: application/json' );
$response = '{}';
if ( class_exists( 'MLApiController' ) ) {
	$api = new MLApiController();
	$api->set_error_handlers( $debug );

	$custom_response = apply_filters( 'mobiloud_custom_posts_results', null );

	if ( ! empty( $custom_response ) ) {
		$response = $custom_response;
	} else {
		$response = $api->handle_request();
	}
}

echo $response;
