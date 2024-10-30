<?php

/**
 * v1 menu category functions
 */

/**
 * Get menu v1 categories
 *
 * @return array
 */
function ml_categories() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_categories';
	$cats       = $wpdb->get_results( "SELECT id, cat_ID FROM $table_name" );
	$categories = array();
	foreach ( $cats as $cat ) {
		$c = get_category( $cat->cat_ID );
		array_push( $categories, $c );
	}

	return $categories;
}

/**
 * Get menu v1 category
 *
 * @param int $ml_catid
 * @return array
 */
function ml_get_category( $ml_catid ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_categories';

	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", absint( $ml_catid ) ) );
}

/**
 * Update menu v1 category
 *
 * @param int $id
 * @param int $cat_ID
 */
function ml_update_cat_ID( $id, $cat_ID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_categories';

	$wpdb->update(
		$table_name,
		array( 'cat_ID' => absint( $cat_ID ) ),
		array( 'ID' => absint( $id ) ),
		array( '%d' ),
		array( '%d' )
	);
}

/**
 * Add category
 *
 * @param int $cat_ID
 */
function ml_add_category( $cat_ID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_categories';
	$wpdb->insert( $table_name, array( 'cat_ID' => absint( $cat_ID ) ), array( '%d' ) );
}

/**
 * Remove category
 *
 * @param int $cat_ID
 */
function ml_remove_category( $cat_ID ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_categories';

	$wpdb->query(
		$wpdb->prepare( "DELETE FROM $table_name WHERE cat_ID = %d", absint( $cat_ID ) )
	);
}

function ml_remove_all_categories() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_categories';

	$wpdb->query( "DELETE FROM $table_name" );
}



/**
 * Switch categories.
 * a.cat_ID <-> b.cat_ID
 *
 * @param int $cat_ID_a
 * @param int $cat_ID_b
 */
function ml_switch_categories( $cat_ID_a, $cat_ID_b ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'mobiloud_categories';

	// getting rows id
	$a_id = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE cat_ID = %d limit 1", absint( $cat_ID_a ) ) )->id;
	$b_id = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE cat_ID = %d limit 1", absint( $cat_ID_b ) ) )->id;

	ml_update_cat_ID( $a_id, $cat_ID_b );
	ml_update_cat_ID( $b_id, $cat_ID_a );
}


