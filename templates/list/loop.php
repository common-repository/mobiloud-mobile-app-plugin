<?php
/**
 * This is a list template: loop.php.
 *
 * It choose and include one of specialized templates (search, favorites, custom-(parameter), custom or regular) using request parameters.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/list
 * @version 4.2.0
 *
 * @global list_type
 */

$debug = false;

$list_type = 'regular';
$list_slug = ''; // empty string or something like "-123".
// phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.VIP.SuperGlobalInputUsage.AccessDetected
if ( isset( $_GET['search'] ) ) {
	$list_type = 'search';
} elseif ( isset( $_GET['post_ids'] ) ) {
	$list_type = 'favorites';
} elseif ( isset( $_GET['post_type'] ) ) {
	$list_type = 'custom';
	$list_slug = '-' . sanitize_title( wp_unslash( $_GET['post_type'] ) );
}
// phpcs:enable WordPress.CSRF.NonceVerification.NoNonceVerification, WordPress.VIP.SuperGlobalInputUsage.AccessDetected
$GLOBALS['list_type'] = $list_type;
flush();

// choose and include the whole html template.
$_names   = [ "{$list_type}{$list_slug}", $list_type, 'regular' ];
$template = Mobiloud::use_template( 'list', $_names, false );
if ( '' !== $template ) {
	require $template;
}
