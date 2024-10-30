<?php

/**
 * Function for compability with oldest iOS Apps.
 * WordPress core is not activated.
 *
 * @param string $endpoint
 */
function ml_compability_api_result( $endpoint, $subdir = false ) {
	// prepare parameters
	$parts        = explode( '/', $_SERVER['SCRIPT_NAME'] );
	$count        = $subdir ? 5 : 4;
	$path         = count( $parts ) > $count + 1 ? implode( '/', array_slice( $parts, 0, count( $parts ) - $count ) ) : '';
	$scheme       = isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ( isset( $_SERVER['REQUEST_SCHEME'] ) ? $_SERVER['REQUEST_SCHEME'] : '' );
	$endpoint_url = ( $scheme ? "$scheme:" : '' ) . '//' . $_SERVER['HTTP_HOST'] . $path . '/ml-api/v2/' . $endpoint;
	$get_params   = '';
	$current_url  = $_SERVER['REQUEST_URI'];
	if ( false !== strpos( $current_url, '?' ) ) {
		$list       = explode( '?', $current_url, 2 );
		$get_params = $list[1];
	}

	$params = file_get_contents( 'php://input' );
	if ( '' !== $get_params ) {
		$params = $get_params . ( '' !== $params ? '&' . $params : '' );
	}
	if ( '' !== $params ) {
		$endpoint_url .= '?' . $params;
	}

	header( 'Location: ' . $endpoint_url, true, 302 );
	die();
}
