<?php
/**
 * v1 menu page functions
 */

/**
 * Return all v1 menu pages
 */
function ml_pages() {
	global $wpdb;
	$table_name  = $wpdb->prefix . 'mobiloud_pages';
	$pages       = $wpdb->get_results( "SELECT id, page_ID, ml_render FROM $table_name" );
	$final_pages = array();
	foreach ( $pages as $page ) {
		$p = get_page( $page->page_ID );
		array_push( $final_pages, $p );
	}

	return $final_pages;
}

/**
 * @deprecated
 * @param int $page_ID
 * @param int $on 0 or 1
 */
function ml_page_change_render( $page_ID, $on ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_pages';
	$wpdb->update( $table_name, array( 'ml_render' => $on ), array( '%d' ) );
}

/**
 * @deprecated
 * @param int $page_ID
 */
function ml_page_render_off( $page_ID ) {
	ml_page_change_render( absint( $page_ID ), 0 );
}

/**
 * @deprecated
 * @param int $page_ID
 */
function ml_page_render_on( $page_ID ) {
	ml_page_change_render( $page_ID, 1 );
}

function ml_page_get_render( $page_ID ) {
	$page = ml_get_page( $page_ID );

	return ( $page->ml_render == 1 || $page->ml_render == '1' );
}

function ml_get_page( $page_ID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_pages';

	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE page_ID = %d", absint( $page_ID ) ) );
}

/**
 * Add page to v1 menu
 *
 * @param int $page_ID
 */
function ml_add_page( $page_ID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_pages';
	$wpdb->insert( $table_name, array( 'page_ID' => absint( $page_ID ) ), array( '%d' ) );
}

/**
 * @deprecated
 * @param int $page_ID
 */
function ml_remove_page( $page_ID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_pages';

	$wpdb->query(
		$wpdb->prepare( "DELETE FROM $table_name WHERE page_ID = %d", absint( $page_ID ) )
	);
}

/**
 * Remove all pages items (v1 menu)
 */
function ml_remove_all_pages() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_pages';

	$wpdb->query( "DELETE FROM $table_name" );
}


