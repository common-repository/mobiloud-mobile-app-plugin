<?php

/**
 * Registers the `list_builder` post type.
 */
function mobiloud_list_builder_init() {
	$is_mobiloud_activated = get_option( 'ml_activated' );
	$step = isset( $_GET['step'] ) ? $_GET['step'] : false;

	if ( false === $is_mobiloud_activated ) {
		return;
	}

	register_post_type( 'list-builder', array(
		'labels'                => array(
			'name'                  => __( 'App List', 'mobiloud-mobile-app-plugin' ),
			'singular_name'         => __( 'List', 'mobiloud-mobile-app-plugin' ),
			'all_items'             => __( 'App Lists', 'mobiloud-mobile-app-plugin' ),
			'archives'              => __( 'App List Archives', 'mobiloud-mobile-app-plugin' ),
			'attributes'            => __( 'App List Attributes', 'mobiloud-mobile-app-plugin' ),
			'insert_into_item'      => __( 'Insert into App List', 'mobiloud-mobile-app-plugin' ),
			'uploaded_to_this_item' => __( 'Uploaded to this App List', 'mobiloud-mobile-app-plugin' ),
			'featured_image'        => _x( 'Featured Image', 'list-builder', 'mobiloud-mobile-app-plugin' ),
			'set_featured_image'    => _x( 'Set featured image', 'list-builder', 'mobiloud-mobile-app-plugin' ),
			'remove_featured_image' => _x( 'Remove featured image', 'list-builder', 'mobiloud-mobile-app-plugin' ),
			'use_featured_image'    => _x( 'Use as featured image', 'list-builder', 'mobiloud-mobile-app-plugin' ),
			'filter_items_list'     => __( 'Filter App List', 'mobiloud-mobile-app-plugin' ),
			'items_list_navigation' => __( 'App List navigation', 'mobiloud-mobile-app-plugin' ),
			'items_list'            => __( 'App List', 'mobiloud-mobile-app-plugin' ),
			'new_item'              => __( 'New App List', 'mobiloud-mobile-app-plugin' ),
			'add_new'               => __( 'Add New', 'mobiloud-mobile-app-plugin' ),
			'add_new_item'          => __( 'Add New App List', 'mobiloud-mobile-app-plugin' ),
			'edit_item'             => __( 'Edit App List', 'mobiloud-mobile-app-plugin' ),
			'view_item'             => __( 'View App List', 'mobiloud-mobile-app-plugin' ),
			'view_items'            => __( 'View App Lists', 'mobiloud-mobile-app-plugin' ),
			'search_items'          => __( 'Search App Lists', 'mobiloud-mobile-app-plugin' ),
			'not_found'             => __( 'No App List found', 'mobiloud-mobile-app-plugin' ),
			'not_found_in_trash'    => __( 'No App List found in trash', 'mobiloud-mobile-app-plugin' ),
			'parent_item_colon'     => __( 'Parent App List:', 'mobiloud-mobile-app-plugin' ),
			'menu_name'             => __( 'App List', 'mobiloud-mobile-app-plugin' ),
		),
		'public'                => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		'show_in_menu'          => 'mobiloud',
		'supports'              => array( 'title', 'editor', 'custom-fields' ),
		'has_archive'           => true,
		'rewrite'               => true,
		'query_var'             => true,
		'menu_position'         => null,
		'menu_icon'             => 'dashicons-admin-post',
		'show_in_rest'          => true,
		'rest_base'             => 'list-builder',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );

	if ( $is_mobiloud_activated && 'welcome-close' === $step ) {
		flush_rewrite_rules();
	}

}
add_action( 'init', 'mobiloud_list_builder_init' );

/**
 * Sets the post updated messages for the `list_builder` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `list_builder` post type.
 */
function list_builder_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['list-builder'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'MobiLoud List updated. <a target="_blank" href="%s">View MobiLoud List</a>', 'mobiloud-mobile-app-plugin' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'mobiloud-mobile-app-plugin' ),
		3  => __( 'Custom field deleted.', 'mobiloud-mobile-app-plugin' ),
		4  => __( 'MobiLoud List updated.', 'mobiloud-mobile-app-plugin' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'MobiLoud List restored to revision from %s', 'mobiloud-mobile-app-plugin' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'MobiLoud List published. <a href="%s">View MobiLoud List</a>', 'mobiloud-mobile-app-plugin' ), esc_url( $permalink ) ),
		7  => __( 'MobiLoud List saved.', 'mobiloud-mobile-app-plugin' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'MobiLoud List submitted. <a target="_blank" href="%s">Preview MobiLoud List</a>', 'mobiloud-mobile-app-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'MobiLoud List scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview MobiLoud List</a>', 'mobiloud-mobile-app-plugin' ),
		date_i18n( __( 'M j, Y @ G:i', 'mobiloud-mobile-app-plugin' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'MobiLoud List draft updated. <a target="_blank" href="%s">Preview MobiLoud List</a>', 'mobiloud-mobile-app-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'list_builder_updated_messages' );

add_action( 'template_include', 'mobiloud_list_builder_template' );
function mobiloud_list_builder_template( $template ) {
	if ( 'list-builder' !== get_post_type() ) {
		return $template;
	}

	return MOBILOUD_PLUGIN_DIR . 'blocks/front-template.php';;
}

/**
 * Renders the helper message for App Lists.
 */
function mobiloud_render_app_list_helper() {
	global $pagenow, $current_screen;

	if ( ! ( 'list-builder' === $current_screen->post_type && 'edit.php' === $pagenow ) ) {
		return;
	}

    ?>
    <div class="notice notice-info mobiloud-admin-notice mobiloud-admin-notice--app-list">
		<h2 ><?php esc_html_e( 'How to use App Lists', 'mobiloud-mobile-app-plugin' ); ?></h2>
        <p><?php printf( __( "App Lists will give you more control over how your content looks in the apps. Through a drag and drop interface you will be able to customize which categories and content should be displayed, as well as the design of your lists. We have created an App List for your app's home screen, you can edit it to see how it looks and works. For more details on how to use App Lists <a href='%s'>click here</a>.", 'mobiloud-mobile-app-plugin' ), 'https://www.mobiloud.com/help/knowledge-base/how-to-use-app-lists' ); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'mobiloud_render_app_list_helper' );

/**
 * AJAX callback to save user option to dismiss helper message permanently.
 */
function mobiloud_admin_notice_post_type() {
	$post_type = filter_input( INPUT_GET, 'postType', FILTER_SANITIZE_STRING );
	$option_name = "mobiloud_admin_notice_{$post_type}";

	update_option( $option_name, true );
}
add_action( 'wp_ajax_mobiloud_admin_notice_post_type', 'mobiloud_admin_notice_post_type' );
