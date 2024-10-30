<?php
ini_set( 'display_errors', 0 );
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( __FILE__ ) . '/api/compability.php';
	ml_compability_api_result( 'comments' );
}
