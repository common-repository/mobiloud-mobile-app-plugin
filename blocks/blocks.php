<?php

/**
 * Returns true if WooCommerce is installed and active.
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) {
			return true;
		} else {
			return false;
		}
	}
}

require_once MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/heading/register.php';
require_once MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/posts/register.php';
require_once MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/divider/register.php';
require_once MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/product-carousel/register.php';
require_once MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/product-search/register.php';
require_once MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/products-from-menu/register.php';
require_once MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/recently-purchased-products/register.php';

function ml_read_attributes_json( $attrs = array(), $path = '' ) {
	$setup = new \Blocks\Setup();
	$block_data = get_values_from_json_attr_keys( $path, $attrs );

	return $block_data;
}

function ml_get_shared_attributes( $attrs = [] ) {
	$shared_attributes = array();

	$shared_attributes['blockHeadingText'] = ml_get_block_attr( $attrs, 'blockHeadingText', '' );
	$shared_attributes['blockHeadingFontFamily'] = ml_get_block_attr( $attrs, 'blockHeadingFontFamily', 'Roboto' );
	$shared_attributes['blockHeadingFontSize'] = ml_get_block_attr( $attrs, 'blockHeadingFontSize', 1.5 );
	$shared_attributes['blockHeadingFontWeight'] = ml_get_block_attr( $attrs, 'blockHeadingFontWeight', '100' );
	$shared_attributes['blockHeadingLineHeight'] = ml_get_block_attr( $attrs, 'blockHeadingLineHeight', 1.8 );
	$shared_attributes['blockHeadingColor'] = ml_get_block_attr( $attrs, 'blockHeadingColor', '#000' );

	return $shared_attributes;
}

function ml_get_block_attr( $attrs = [], $name = '', $default = '' ) {
	if ( ! isset( $attrs[ $name ] ) ) {
		return $default;
	}

	return $attrs[ $name ];
}

add_action( 'init', 'mobiloud_list_builder_rewrite' );
function mobiloud_list_builder_rewrite() {
	add_rewrite_rule( '^ml-api/v2/listbuilder/([0-9]+)/?$', 'index.php?post_type=list-builder&p=$matches[1]' );
	add_rewrite_rule( '^ml-api/v2/app-pages/([0-9]+)/?$', 'index.php?post_type=app-pages&p=$matches[1]' );
}

add_action( 'admin_enqueue_scripts', 'mobiloud_enqueue_block_assets' );
function mobiloud_enqueue_block_assets() {
	$post_type = get_post_type();
	$screen    = get_current_screen();

	if ( 'list-builder' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style( 'mobiloud-posts-block' );

	if ( is_admin() && 'list-builder' === $screen->id ) {
		wp_enqueue_style( 'mobiloud-posts-block-editor-style' );
		wp_localize_script( 'mobiloud-posts-block-editor', 'mobiloudBlockGlobals', array( 'plugins' => array(
			'woocommerce' => is_woocommerce_activated()
		) ) );
	}
}

add_filter( 'allowed_block_types_all', 'mobiloud_app_pages_restrict_blocks', 10, 2 );
function mobiloud_app_pages_restrict_blocks( $allowed_blocks, $block_editor_context ) {
	$curent_screen = get_current_screen();

	if ( 'post' === $curent_screen->base && $block_editor_context->post->post_type !== 'app-pages' ) {
		return $allowed_blocks;
	}

	$default_blocks = array(
		'core/image',
		'core/freeform',
		'mobiloud/divider',
	);

	return $default_blocks;
}

add_filter( 'allowed_block_types_all', 'mobiloud_list_builder_restrict_blocks', 10, 2 );
function mobiloud_list_builder_restrict_blocks( $allowed_blocks, $block_editor_context ) {
	$curent_screen = get_current_screen();

	if ( 'post' === $curent_screen->base && $block_editor_context->post->post_type !== 'list-builder' ) {
		return $allowed_blocks;
	}

	$default_blocks = array(
		'mobiloud/posts',
		'mobiloud/divider',
		'mobiloud/heading',
	);

	$woocommerce_blocks = array(
		'mobiloud/product-carousel',
		'mobiloud/product-search',
		'mobiloud/products-from-menu',
		'mobiloud/recently-purchased-products',
	);

	if ( is_woocommerce_activated() ) {
		$default_blocks = array_merge( $default_blocks, $woocommerce_blocks );
	}

	return $default_blocks;
}

function mobiloud_enqueue_gutenberg_editor_assets( $hook ) {
	$post_type = get_post_type();

	if ( 'list-builder' !== $post_type ) {
		return;
	}
}
add_action( 'enqueue_block_editor_assets', 'mobiloud_enqueue_gutenberg_editor_assets', 1 );

/**
 * Updates the list builder preview URL.
 *
 * @param string  $preview_link URL used for the post preview.
 * @param WP_Post $post         Post object.
 *
 * @return string
 */
function mobiloud_list_builder_preview_url( $preview_link, $post ) {
	$post_type = get_post_type( $post );

	if ( 'list-builder' !== $post_type ) {
		return $preview_link;
	}

	$site_url = get_site_url();
	return sprintf(
		'%s/ml-api/v2/listbuilder/%s',
		$site_url,
		$post->ID
	);
}
add_filter( 'preview_post_link', 'mobiloud_list_builder_preview_url', 10, 2 );

/**
 * Injects script to the list-builder edit screen.
 */
function mobiloud_inject_inline_styles() {
	if ( 'list-builder' !== get_post_type() ) {
		return;
	}

	?>

	<style>
		.editor-post-title__input {
			display: none !important;
		}
	</style>
	<?php
}
add_action( 'admin_print_styles', 'mobiloud_inject_inline_styles' );

add_action( 'after_setup_theme', function() {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Roboto+Slab:wght@100;200;300;400;500;600;700;800;900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap' );
} );

function mobiloud_register_list_builder_global_meta() {
	register_meta( 'post', '_ml_titleColor', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '#000000',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_titleFont', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => 'Roboto',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_titleFontSize', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 1.136,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_titleFontWeight', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '400',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_titleLineHeight', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 1.3,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_metaColor', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '#708090',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_metaFont', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => 'Montserrat',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_metaFontSize', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 0.6785,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_metaFontWeight', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '400',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_metaLineHeight', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 0.92,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_bodyColor', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '#a9a9a9',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_bodyFont', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => 'Open Sans',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_bodyFontSize', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 0.85,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_bodyFontWeight', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '400',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_bodyLineHeight', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 1.2,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_wooPriceColor', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '#666',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_wooPriceFont', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => 'Open Sans',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_wooPriceFontSize', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 0.9,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_wooPriceFontWeight', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '400',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_wooPriceLineHeight', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 1.27,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_headingColor', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '#000',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_headingFont', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => 'Merriweather',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_headingFontSize', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 2.02,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_headingFontWeight', array(
		'show_in_rest'  => true,
		'type'          => 'string',
		'single'        => true,
		'default'       => '700',
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_ml_headingLineHeight', array(
		'show_in_rest'  => true,
		'type'          => 'number',
		'single'        => true,
		'default'       => 1.47,
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_mobiloud_action_filters_status', array(
		'object_subtype' => 'app-pages',
		'show_in_rest'   => true,
		'type'           => 'boolean',
		'single'         => true,
		'default'        => false,
		'auth_callback'  => function() {
			return current_user_can( 'edit_posts' );
		}
	) );

	register_meta( 'post', '_mlglobal_userfontcolors', array(
		'type'          => 'object',
		'single'        => true,
		'default'       => array(
			'headingColorUser' => [],
			'titleColorUser' => [],
			'metaColorUser' => [],
			'bodyColorUser' => [],
			'wooPriceColorUser' => [],
		),
		'show_in_rest' => array(
			'schema' => array(
				'type'       => 'object',
				'properties' => array(
					'bodyColorUser' => array(
						'type' => 'array',
						'items' => array(
							'type' => 'string',
							'format' => 'hex-color',
						),
					),
					'metaColorUser' => array(
						'type' => 'array',
						'items' => array(
							'type' => 'string',
							'format' => 'hex-color',
						),
					),
					'titleColorUser' => array(
						'type' => 'array',
						'items' => array(
							'type' => 'string',
							'format' => 'hex-color',
						),
					),
					'wooPriceColorUser' => array(
						'type' => 'array',
						'items' => array(
							'type' => 'string',
							'format' => 'hex-color',
						),
					),
					'headingColorUser' => array(
						'type' => 'array',
						'items' => array(
							'type' => 'string',
							'format' => 'hex-color',
						),
					),
				),
			),
		),
		'auth_callback' => function() { 
			return current_user_can( 'edit_posts' );
		}
	) );
}
add_action( 'init', 'mobiloud_register_list_builder_global_meta' );

function mobiloud_get_global_doc_meta( $post_id = 0 ) {
	// Heading.
	$heading_color       = get_post_meta( $post_id, '_ml_headingColor', true );
	$heading_font        = get_post_meta( $post_id, '_ml_headingFont', true );
	$heading_font_size   = get_post_meta( $post_id, '_ml_headingFontSize', true );
	$heading_font_weight = get_post_meta( $post_id, '_ml_headingFontWeight', true );
	$heading_line_height = get_post_meta( $post_id, '_ml_headingLineHeight', true );

	// Title.
	$title_color       = get_post_meta( $post_id, '_ml_titleColor', true );
	$title_font        = get_post_meta( $post_id, '_ml_titleFont', true );
	$title_font_size   = get_post_meta( $post_id, '_ml_titleFontSize', true );
	$title_font_weight = get_post_meta( $post_id, '_ml_titleFontWeight', true );
	$title_line_height = get_post_meta( $post_id, '_ml_titleLineHeight', true );
	
	// Meta.
	$meta_color       = get_post_meta( $post_id, '_ml_metaColor', true );
	$meta_font        = get_post_meta( $post_id, '_ml_metaFont', true );
	$meta_font_size   = get_post_meta( $post_id, '_ml_metaFontSize', true );
	$meta_font_weight = get_post_meta( $post_id, '_ml_metaFontWeight', true );
	$meta_line_height = get_post_meta( $post_id, '_ml_metaLineHeight', true );

	// Body.
	$body_color       = get_post_meta( $post_id, '_ml_bodyColor', true );
	$body_font        = get_post_meta( $post_id, '_ml_bodyFont', true );
	$body_font_size   = get_post_meta( $post_id, '_ml_bodyFontSize', true );
	$body_font_weight = get_post_meta( $post_id, '_ml_bodyFontWeight', true );
	$body_line_height = get_post_meta( $post_id, '_ml_bodyLineHeight', true );

	// Woo Price.
	$woo_price_color       = get_post_meta( $post_id, '_ml_wooPriceColor', true );
	$woo_price_font        = get_post_meta( $post_id, '_ml_wooPriceFont', true );
	$woo_price_font_size   = get_post_meta( $post_id, '_ml_wooPriceFontSize', true );
	$woo_price_font_weight = get_post_meta( $post_id, '_ml_wooPriceFontWeight', true );
	$woo_price_line_height = get_post_meta( $post_id, '_ml_wooPriceLineHeight', true );

	return array(
		'titleColor'          => $title_color,
		'titleFont'           => $title_font,
		'titleFontSize'       => $title_font_size,
		'titleFontWeight'     => $title_font_weight,
		'titleLineHeight'     => $title_line_height,
		'metaColor'           => $meta_color,
		'metaFont'            => $meta_font,
		'metaFontSize'        => $meta_font_size,
		'metaFontWeight'      => $meta_font_weight,
		'metaLineHeight'      => $meta_line_height,
		'bodyColor'           => $body_color,
		'bodyFont'            => $body_font,
		'bodyFontSize'        => $body_font_size,
		'bodyFontWeight'      => $body_font_weight,
		'bodyLineHeight'      => $body_line_height,
		'wooPriceColor'       => $woo_price_color,
		'wooPriceFont'        => $woo_price_font,
		'wooPriceFontSize'    => $woo_price_font_size,
		'wooPriceFontWeight'  => $woo_price_font_weight,
		'wooPriceLineHeight'  => $woo_price_line_height,
		'headingColor'       => $heading_color,
		'headingFont'        => $heading_font,
		'headingFontSize'   => $heading_font_size,
		'headingFontWeight' => $heading_font_weight,
		'headingLineHeight' => $heading_line_height,
	);
}

function mobiloud_get_list_builder_fonts() {
	return [
		array (
			'label' => 'Roboto',
			'value' => 'Roboto',
		),
		array (
			'label' => 'Roboto Condensed',
			'value' => 'Roboto Condensed',
		),
		array (
			'label' => 'Open Sans',
			'value' => 'Open Sans',
		),
		array (
			'label' => 'Montserrat',
			'value' => 'Montserrat',
		),
		array (
			'label' => 'Merriweather',
			'value' => 'Merriweather',
		),
		array (
			'label' => 'Roboto Slab',
			'value' => 'Roboto Slab',
		),
		array (
			'label' => 'Playfair Display',
			'value' => 'Playfair Display',
		),
		array (
			'label' => 'Libre Baskerville',
			'value' => 'Libre Baskerville',
		),
	];
}

function mobiloud_generate_google_font_links() {
	$fonts_array = mobiloud_get_list_builder_fonts();
	$link_tag_str = '<link rel="preconnect" href="https://fonts.gstatic.com">';
	$query_params = array();

	foreach ( $fonts_array as $font ) {
		$query_params[] = sprintf(
			'family=%s',
			preg_replace( '/\s+/', '+', $font['value'] )
		);
	}

	return sprintf(
		'%s<link href="https://fonts.googleapis.com/css2?%s&display=swap" rel="stylesheet">',
		$link_tag_str,
		implode( '&', $query_params )
	);
}

/**
 * Filters the array of row action links on the Posts list table.
 *
 * @param array   $actions An array of row action links. Defaults are 'Edit', 'Quick Edit', 'Restore', 'Trash', 'Delete Permanently', 'Preview', and 'View'.
 * @param WP_Post $post      The post in question.
 * @param bool    $leavename Whether to keep the post name.
 */
function mobiloud_filter_post_link( $actions, $post ) {
	if ( 'list-builder' !== $post->post_type ) {
		return $actions;
	}

	$actions['view'] = sprintf(
		'<a href="%s" rel="bookmark" aria-label="View &#8220;List Green&#8221;">%s</a>',
		get_site_url() . '/ml-api/v2/listbuilder/' . $post->ID,
		__( 'View' )
	);

	return $actions;
}
add_filter( 'post_row_actions', 'mobiloud_filter_post_link', 10, 2 );

/**
 * Change type 'list' to 'link'
 */
function mobiloud_filter_config_item_data( $item_data ) {
	if ( 'list' === $item_data['type'] ) {
		$item_data['type'] = 'link';
	}

	return $item_data;
}
add_filter( 'ml_get_menu_config_item', 'mobiloud_filter_config_item_data', 10, 1 );

/**
 * Add default blocks.
 *
 * @param string  $post_content Default post content.
 * @param WP_Post $post         Post object.
 */
function add_default_blocks( $post_content, $post ) {
	if ( 'list-builder' !== $post->post_type ) {
		return $post_content;
	}

	$post_content = '<!-- wp:mobiloud/heading {"fontFamily":"Merriweather","fontSize":2.7,"titleText":"List of Posts"} /-->

	<!-- wp:mobiloud/posts {"infiniteScroll":true,"showTaxonomies":{"category":true,"post_tag":true}} /-->';

	return $post_content;
}
add_filter( 'default_content', 'add_default_blocks', 10, 2 );

/**
 * Enables block editor on the List Builder page.
 *
 * @param bool    $use_block_editor Whether the post can be edited or not.
 * @param WP_Post $post             The post being checked.
 */
function mobiloud_enable_gutenberg_for_list_builder( $use_block_editor, $post ) {
	return 'list-builder' === $post->post_type ? true : $use_block_editor;
}
add_filter( 'use_block_editor_for_post', 'mobiloud_enable_gutenberg_for_list_builder', 999, 2 );

/**
 * Adds/removes list builder post type list columns.
 *
 * @param string[] $columns An associative array of column headings.
 * @return array
 */
function mobiloud_manage_list_builder_columns( $columns ) {
	unset( $columns['date'] );
	return $columns;
}
add_filter( 'manage_list-builder_posts_columns' , 'mobiloud_manage_list_builder_columns' );

/**
 * @param string[] $actions An array of row action links. Defaults are 'Edit', 'Quick Edit', 'Restore', 'Trash', 'Delete Permanently', 'Preview', and 'View'.
 * @param WP_Post  $post    The post object.
 */
function mobiloud_manage_post_row_options( $actions, $post ) {
	if ( 'list-builder' !== $post->post_type ) {
		return $actions;
	}

	unset( $actions['inline hide-if-no-js'] );
	return $actions;
}
add_filter( 'post_row_actions' , 'mobiloud_manage_post_row_options', 10, 2 );

/**
 * Filters the list of available list table views.
 *
 * @param string[] $views An array of available list table views.
 * @return string[]
 */
add_filter( 'views_edit-list-builder' , function( $view_options ) {
	unset( $view_options['publish'] );
	return $view_options;
}, 10 );

function mobiloud_list_builder_table_css() {
	$screen = get_current_screen();
	$is_list_builder = 'edit-list-builder' === $screen->id && 'list-builder' === $screen->post_type;
	$is_app_list     = 'edit-app-pages' === $screen->id && 'app-pages' === $screen->post_type;

	if ( $is_list_builder || $is_app_list ) :
		$screen->remove_help_tabs();
	?>
		<style>
			.search-box {
				display: none;
			}

			.tablenav .actions, .tablenav.top {
				display: none;
			}

			.check-column {
				display: none;
			}
		</style>
	<?php endif;
}
add_action( 'admin_head', 'mobiloud_list_builder_table_css' );

/**
 * Filters whether to show the Screen Options tab.
 *
 * @param bool      $show_screen Whether to show Screen Options tab. Default true.
 * @param WP_Screen $screen      Current WP_Screen instance.
 */
function mobiloud_manage_screen_options( $show_screen, $screen ) {
	if ( 'edit-list-builder' === $screen->id && 'list-builder' === $screen->post_type ) {
		return false;
	}

	return $show_screen;
}
add_filter( 'screen_options_show_screen', 'mobiloud_manage_screen_options', 10, 2 );
