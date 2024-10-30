<?php
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( __FILE__ ) . '/api/compability.php';
	ml_compability_api_result( 'page' );
}

if ( isset( $_GET['page_ID'] ) ) {
	$page_ID = sanitize_text_field( $_GET['page_ID'] );
} else if ( isset( $_GET['post_id'] ) ) {
	$page_ID = sanitize_text_field( $_GET['post_id'] );
}



$page = get_post( $page_ID );


if ( isset( $_GET['full'] ) ) {
	// redirect
	$link = get_permalink( $page_ID );
	header( "Location: $link" );
	exit;
}

$post    = $page;
$post_id = $page_ID;
require 'post/post.php';
