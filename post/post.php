<?php
if ( ! defined( 'MOBILOUD_API_REQUEST' ) ) {
	require_once dirname( dirname( __FILE__ ) ) . '/api/compability.php';
	ml_compability_api_result( 'post', true );
}
// GET params used by app to query for posts / pages.
// This is part of an API endpoint, as are all nonce errors that were whitelisted with ignore flags.
if ( ( ! isset( $_GET['post_id'] ) ) && ( ! isset( $_GET['page_ID'] ) ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
	header( 'HTTP/1.1 404 Not Found' );
	exit;
}

if ( extension_loaded( 'newrelic' ) ) {
	newrelic_disable_autorum();
}

if ( empty( $post_id ) ) {
	$post_id = htmlspecialchars( esc_attr( sanitize_text_field( $_GET['post_id'] ) ) ); // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
	$post    = get_post( $post_id );
}

if ( empty( $post ) ) {
	header( 'HTTP/1.1 404 Not Found' );
	exit;
}

if ( Mobiloud::get_option( 'ml_exclude_posts_enabled' ) ) { // exclude posts from lists enabled.
	if ( Mobiloud::is_post_excluded_from_list( $post->ID ) ) {
		header( 'HTTP/1.1 404 Not Found' );
		exit;
	}
}

if ( empty( $_GET['related_posts'] ) && empty( $_GET['related'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
	// we will use this variable in templates.
	$ml_post_type = $post->post_type;
	$template     = Mobiloud::use_template( 'views', [ $ml_post_type, 'post' ], false );
	include $template;
} else {
	// related posts for post with ID from $post->ID.
	$ml_post_type = $post->post_type;
	$template     = Mobiloud::use_template( 'related', [ 'content-' . $ml_post_type, 'content' ], false );
	include $template;
	exit;
}
