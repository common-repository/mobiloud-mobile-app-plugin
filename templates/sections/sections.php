<?php
/**
 * This is a sections template: sections.php.
 *
 * It uses body.php template for <body>...</body> content.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/sections
 * @version 4.2.4
 */

ini_set( 'display_errors', 0 );

$debug = false;

flush();

/**
 * Add custom menu fields to menu.
 *
 * @param mixed $menu_item
 */
function ml_menu_add_custom_nav_fields( $menu_item ) {

	$menu_item->opening_method = get_post_meta( $menu_item->ID, '_ml_menu_item_opening_method', true );
	return $menu_item;

}
add_filter( 'wp_setup_nav_menu_item', 'ml_menu_add_custom_nav_fields' );

function getMenuItemsForParent( $menuSlug, $parentId ) {
	$args     = [
		'post_type'  => 'nav_menu_item',
		'meta_key'   => '_menu_item_menu_item_parent',
		'meta_value' => $parentId,
		'tax_query'  => [
			[
				'taxonomy' => 'nav_menu',
				'field'    => 'slug',
				'terms'    => [ $menuSlug ],
			],
		],
		'order'      => 'ASC',
		'orderby'    => 'menu_order',
	];
	$tmpItems = query_posts( $args );

	$items = [];
	foreach ( $tmpItems as $tmpItem ) {
		$item           = new stdClass();
		$type           = get_post_meta( $tmpItem->ID, '_menu_item_type', true );
		$object         = get_post_meta( $tmpItem->ID, '_menu_item_object', true );
		$object_id      = get_post_meta( $tmpItem->ID, '_menu_item_object_id', true );
		$opening_method = get_post_meta( $tmpItem->ID, '_ml_menu_item_opening_method', true );

		if ( empty( $opening_method ) ) {
			$opening_method = 'native';
		}

		switch ( $type ) :

			case 'post_type':
				$postId     = get_post_meta( $tmpItem->ID, '_menu_item_object_id', true );
				$post       = get_post( $postId );
				$item->name = '' !== $tmpItem->post_title ? $tmpItem->post_title : $post->post_title;
				$item->url  = get_permalink( $postId );
				break;

			case 'taxonomy':
				$catID      = get_post_meta( $tmpItem->ID, '_menu_item_object_id', true );
				$tax        = get_post_meta( $tmpItem->ID, '_menu_item_object', true );
				$cat        = get_term( $catID, $tax );
				$item->name = '' !== $tmpItem->post_title ? $tmpItem->post_title : $cat->name;
				break;

			case 'custom':
				$item->name = $tmpItem->post_title;
				$item->url  = get_post_meta( $tmpItem->ID, '_menu_item_url', true );
				break;
		endswitch;
		$item->type           = $type;
		$item->object         = $object;
		$item->object_id      = $object_id;
		$item->opening_method = $opening_method;
		$item->children       = getMenuItemsForParent( $menuSlug, $tmpItem->ID );
		$items[]              = $item;
	}

	return $items;
}

// Get Sections menu.
$sections_menu = Mobiloud::get_option( 'ml_sections_menu' );

?><!DOCTYPE html>
<html>
	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">

		<?php

		function ml_sections_stylesheets() {
			wp_enqueue_style( 'onsenui', MOBILOUD_PLUGIN_URL . 'libs/onsen/css/onsenui.min.css' );
			wp_enqueue_style( 'onsen-components', MOBILOUD_PLUGIN_URL . 'libs/onsen/css/onsen-css-components.min.css' );
			wp_enqueue_style( 'mobiloud-sections', MOBILOUD_PLUGIN_URL . 'templates/sections/css/sections.css' );
			wp_enqueue_style( 'mobiloud-typeplate', MOBILOUD_PLUGIN_URL . 'templates/sections/css/_typeplate.css' );
		}

		function ml_sections_scripts() {
			wp_enqueue_script( 'onsenui', MOBILOUD_PLUGIN_URL . 'libs/onsen/js/onsenui.min.js', array(), false, true );
		}

		/**
		 * Prepend "data-cfasync" parameter to scripts.
		 *
		 * @param string $tag
		 * @param string $handle
		 */
		function ml_sections_add_data_attribute( $tag, $handle ) {
			if ( in_array( $handle, array( 'onsenui' ), true ) ) {
				return str_replace( ' src', ' data-cfasync="false" src', $tag );
			}
			return $tag;
		}
		add_filter( 'script_loader_tag', 'ml_sections_add_data_attribute', 10, 2 );


		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_footer' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_enqueue_scripts' );
		remove_all_actions( 'locale_stylesheet' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_print_footer_scripts' );
		remove_all_actions( 'wp_shortlink_wp_head' );

		add_action( 'wp_print_styles', 'ml_sections_stylesheets' );
		add_action( 'wp_print_footer_scripts', 'ml_sections_scripts', 30 );
		add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 30 );

		add_action( 'wp_head', 'wp_print_styles' );
		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

		wp_head();

		$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
		echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		?>
	</head>
	<?php
	$body_classes = array( 'ml-sections', Mobiloud::get_template_class( __FILE__, 'ml-sections-' ) );
	/**
	* Filter body classes list for the section.
	*
	* @since 4.2.0
	*
	* @param string[] $body_classes  Array with class names.
	* @param string   $template_type Template type where it called: 'list', 'comments', etc.
	*/
	$body_classes = apply_filters( 'mobiloud_body_class', $body_classes, 'sections' );
	?>
	<body class="<?php echo esc_attr( implode( ' ', $body_classes ) ); ?>">
		<?php
		$template = Mobiloud::use_template( 'sections', 'body', false );
		require_once $template;
		?>
	</body>
</html>
