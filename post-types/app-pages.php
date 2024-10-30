<?php

/**
 * Registers the `app_pages` post type.
 */
function mobiloud_app_pages_init() {
	$is_mobiloud_activated = get_option( 'ml_activated' );
	$step = isset( $_GET['step'] ) ? $_GET['step'] : false;

	if ( false === $is_mobiloud_activated ) {
		return;
	}

	register_post_type( 'app-pages', array(
		'labels'                => array(
			'name'                  => __( 'App Pages', 'mobiloud-mobile-app-plugin' ),
			'singular_name'         => __( 'App Page', 'mobiloud-mobile-app-plugin' ),
			'all_items'             => __( 'App Pages', 'mobiloud-mobile-app-plugin' ),
			'archives'              => __( 'App Page Archives', 'mobiloud-mobile-app-plugin' ),
			'attributes'            => __( 'App Page Attributes', 'mobiloud-mobile-app-plugin' ),
			'insert_into_item'      => __( 'Insert into App Page', 'mobiloud-mobile-app-plugin' ),
			'uploaded_to_this_item' => __( 'Uploaded to this App Page', 'mobiloud-mobile-app-plugin' ),
			'featured_image'        => _x( 'Featured Image', 'app-pages', 'mobiloud-mobile-app-plugin' ),
			'set_featured_image'    => _x( 'Set featured image', 'app-pages', 'mobiloud-mobile-app-plugin' ),
			'remove_featured_image' => _x( 'Remove featured image', 'app-pages', 'mobiloud-mobile-app-plugin' ),
			'use_featured_image'    => _x( 'Use as featured image', 'app-pages', 'mobiloud-mobile-app-plugin' ),
			'filter_items_list'     => __( 'Filter App Pages list', 'mobiloud-mobile-app-plugin' ),
			'items_list_navigation' => __( 'App Pages list navigation', 'mobiloud-mobile-app-plugin' ),
			'items_list'            => __( 'App Pages list', 'mobiloud-mobile-app-plugin' ),
			'new_item'              => __( 'New App Page', 'mobiloud-mobile-app-plugin' ),
			'add_new'               => __( 'Add New', 'mobiloud-mobile-app-plugin' ),
			'add_new_item'          => __( 'Add New App Page', 'mobiloud-mobile-app-plugin' ),
			'edit_item'             => __( 'Edit App Page', 'mobiloud-mobile-app-plugin' ),
			'view_item'             => __( 'View App Page', 'mobiloud-mobile-app-plugin' ),
			'view_items'            => __( 'View App Pages', 'mobiloud-mobile-app-plugin' ),
			'search_items'          => __( 'Search App Pages', 'mobiloud-mobile-app-plugin' ),
			'not_found'             => __( 'No App Pages found', 'mobiloud-mobile-app-plugin' ),
			'not_found_in_trash'    => __( 'No App Pages found in trash', 'mobiloud-mobile-app-plugin' ),
			'parent_item_colon'     => __( 'Parent App Page:', 'mobiloud-mobile-app-plugin' ),
			'menu_name'             => __( 'App Pages', 'mobiloud-mobile-app-plugin' ),
		),
		'public'                => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		'show_in_menu'          => 'mobiloud',
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'has_archive'           => true,
		'rewrite'               => true,
		'query_var'             => true,
		'menu_position'         => null,
		'menu_icon'             => 'dashicons-admin-post',
		'show_in_rest'          => true,
		'rest_base'             => 'app-pages',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );

	if ( $is_mobiloud_activated && 'welcome-close' === $step ) {
		flush_rewrite_rules();
	}

}
add_action( 'init', 'mobiloud_app_pages_init' );

/**
 * Sets the post updated messages for the `app_pages` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `app_pages` post type.
 */
function app_pages_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['app-pages'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'App Page updated. <a target="_blank" href="%s">View App Page</a>', 'mobiloud-mobile-app-plugin' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'mobiloud-mobile-app-plugin' ),
		3  => __( 'Custom field deleted.', 'mobiloud-mobile-app-plugin' ),
		4  => __( 'App Page updated.', 'mobiloud-mobile-app-plugin' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'App Page restored to revision from %s', 'mobiloud-mobile-app-plugin' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'App Page published. <a href="%s">View App Page</a>', 'mobiloud-mobile-app-plugin' ), esc_url( $permalink ) ),
		7  => __( 'App Page saved.', 'mobiloud-mobile-app-plugin' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'App Page submitted. <a target="_blank" href="%s">Preview App Page</a>', 'mobiloud-mobile-app-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'App Page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview App Page</a>', 'mobiloud-mobile-app-plugin' ),
		date_i18n( __( 'M j, Y @ G:i', 'mobiloud-mobile-app-plugin' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'App Page draft updated. <a target="_blank" href="%s">Preview App Page</a>', 'mobiloud-mobile-app-plugin' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'app_pages_updated_messages' );

add_action( 'template_include', 'mobiloud_app_pages_template' );
function mobiloud_app_pages_template( $template ) {
	if ( 'app-pages' !== get_post_type() ) {
		return $template;
	}

	return Mobiloud::get_default_template( 'page' );
}

/**
 * Renders the helper message for App Pages.
 */
add_action( 'admin_notices', 'mobiloud_render_app_pages_helper' );
function mobiloud_render_app_pages_helper() {
	global $pagenow, $current_screen;

	if ( ! ( 'app-pages' === $current_screen->post_type && 'edit.php' === $pagenow ) ) {
		return;
	}

    ?>
    <div class="notice notice-info mobiloud-admin-notice mobiloud-admin-notice--app-pages">
		<h2><?php esc_html_e( 'How to use App Pages', 'mobiloud-mobile-app-plugin' ); ?></h2>
        <p><?php printf( __( "App pages are intended to give you an easy way to create content that is optimized for your app, they will usually load a lot faster and have a clean design. You will want to use App Pages for the following types of pages: privacy policy, about us, contact us, etc. For more details on how to use App Pages <a href='%s'>click here</a>.", 'mobiloud-mobile-app-plugin' ), 'https://www.mobiloud.com/help/knowledge-base/how-to-use-app-pages' ); ?></p>
    </div>
    <?php
}
