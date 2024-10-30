<?php
namespace Blocks;

function get_block_posts_data( $query_attrs = array(), $extra_args = array() ) {
	$query_arg = array(
		'order'          => 'DESC',
		'orderby'        => 'date',
		'post_type'      => 'post',
		'posts_per_page' => 10,
	);

	// search by string.
	if ( isset( $query_attrs['searchString'] ) ) {
		$query_arg['s'] = $query_attrs['searchString'];
	}

	// Order.
	if ( isset( $query_attrs['order'] ) ) {
		$query_arg['order'] = $query_attrs['order'];
	}

	// Orderby.
	if ( isset( $query_attrs['orderby'] ) ) {
		$query_arg['orderby'] = $query_attrs['orderby'];
	}

	// Post type.
	if ( isset( $query_attrs['currentPostType'] ) ) {
		$query_arg['post_type'] = $query_attrs['currentPostType'];
	}

	// Per page.
	if ( isset( $query_attrs['postCount'] ) ) {
		$query_arg['posts_per_page'] = (int)$query_attrs['postCount'];
	}

	// List of post IDs.
	if ( isset( $query_attrs['postIds'] ) ) {
		$query_arg['post__in'] = $query_attrs['postIds'];
	}

	// Taxonomies.
	if ( isset( $query_attrs['selectedTerms'] ) && is_array( $query_attrs['selectedTerms'] ) ) {
		$query_arg['tax_query']['relation'] = 'AND';
		foreach ( $query_attrs['selectedTerms'] as $tax_slug => $term_array ) {
			if ( empty( $query_attrs['selectedTerms'][ $tax_slug ] ) ) {
				continue;
			}

			$query_arg['tax_query'][] = array(
				'taxonomy' => $tax_slug,
				'field'    => 'term_id',
				'terms'    => array_map( function( $term ) {
					return $term['value'];
				}, $query_attrs['selectedTerms'][ $tax_slug ] ),
			);
		}
	}

	// Final query args.
	$query_arg = array_merge( $query_arg, $extra_args );

	// Final query.
	$query = new \WP_Query( $query_arg );

	// array to hold final data.
	$posts = array();

	// while loop counter.
	$counter = 0;

	$posts['posts'] = array();

	while ( $query->have_posts() ) {
		$query->the_post();

		$post_id = get_the_ID();

		// initialise $posts array with final data.
		$posts['posts'][] = generate_post_item_by_id( $post_id, $query_attrs, $counter );

		$counter++;
	}

	wp_reset_postdata();

	return $posts;
}

function generate_post_item_by_id( $post_id = 0, $query_attrs = array(), $counter = 0 ) {
	$post = get_post( $post_id );

	// Add excerpt.
	$excerpt = get_the_excerpt( $post_id );
	$excerpt = empty( $excerpt ) ? get_the_content( null, false, $post_id ) : $excerpt;

	// Add title, excerpt and taxonomies with terms for the current post.
	$temp_post = array(
		'title'   => get_the_title( $post_id ),
		'excerpt' => $excerpt,
		'taxonomiesWithTerms' => get_taxonomies_with_terms( $post_id ),
	);

	// Add id.
	$temp_post['id'] = $post_id;

	// Add permalink.
	$temp_post['url'] = get_the_permalink( $post_id );

	// Add author.
	$temp_post['author'] = get_author_info( $post->post_author );

	// Add featured image.
	$thumbnail_size = 'thumbnail';

	// decide thumbnail size based on `displayAs` parameter.
	if ( isset( $query_attrs['displayAs'] ) ) {
		if ( 'grid' === $query_attrs['displayAs'] ) {
			$thumbnail_size = 'medium';
		} else if ( 'list' === $query_attrs['displayAs'] ) {
			$thumbnail_size = 'thumbnail';
		} else if ( 'list-expanded' === $query_attrs['displayAs'] ) {
			$thumbnail_size = 'medium_large';
		}
	}

	// decide thumbnail size based on `highlightFirstPost` parameter.
	if ( isset( $query_attrs['highlightFirstPost'] ) && ( 'true' === $query_attrs['highlightFirstPost'] || true === $query_attrs['highlightFirstPost'] ) && 0 === $counter ) {
		$thumbnail_size = 'medium_large';
	}

	// add post thumbnail.
	$temp_post['imageInfo'] = array(
		'url' => get_the_post_thumbnail_url( $post_id, $thumbnail_size ),
	);

	// Add post date.
	$temp_post['date'] = get_the_date( 'c' );

	// Add post type.
	$temp_post['postType'] = get_post_type( $post_id );

	if ( is_woocommerce_activated() ) {
		$temp_post['productInfo'] = get_product_info( $post_id );
	}

	return $temp_post;
}

function get_product_info( $post_id = 0 ) {
	if ( ! is_woocommerce_activated() ) {
		return false;
	}

	if ( 'product' !== get_post_type( $post_id ) ) {
		return false;
	}

	$product = wc_get_product( $post_id );

	return array(
		'priceHtml' => $product->get_price_html(),
	);
}

/**
 * Returns author name by author ID.
 *
 * @param int Author ID.
 * @return string
 */
function get_author_info( $author_id ) {
	$author_name = get_the_author_meta( 'display_name', $author_id );
	$username = get_the_author_meta( 'user_login', $author_id );

	return array(
		'name'     => $author_name,
		'username' => $username,
	);
}

/**
 * Creates an array with terms mapped to its respective taxonomies.
 *
 * @param int Post ID.
 * @return array
 */
function get_taxonomies_with_terms( $post_id ) {
	$post_type = get_post_type( $post_id );
	$taxonomies = get_object_taxonomies( $post_type, 'objects' );
	$terms = array();

	foreach ( $taxonomies as $taxonomy ) {
		if ( $taxonomy->public ) {
			$terms[ $taxonomy->name ] = array(
				'label' => $taxonomy->label,
				'value' => $taxonomy->name,
				'terms' => get_the_terms( $post_id, $taxonomy->name ),
			);
		}
	}

	return $terms;
}

function get_products_from_menu( $menu_id ) {
	$data = array();
	$menu_array = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
	$menu_array = array_map( function( $menu_obj ) {
		return array(
			'label' => $menu_obj->name,
			'value' => $menu_obj->term_id,
		);
	}, $menu_array );
	$data['menus'] = $menu_array;

	if ( empty( $menu_id ) ) {
		$data['productArray'] = array();

		return $data;
	}

	$menu_items = wp_get_nav_menu_items( $menu_id );
	foreach ( $menu_items as $menu_item ) {
		$item_id = (int)$menu_item->object_id;

		if ( 'product' === get_post_type( $item_id ) ) {
			$data['productArray'][] = generate_post_item_by_id( $item_id );
		}
	}

	return $data;
}

/**
 * Generates paginated data during load more or infinite scroll.
 */
function block_posts_load_more() {
	$attrs      = $_POST['attrs'];
	$page       = (int)$_POST['page'];
	
	$attrs      = json_decode( stripslashes( $attrs ), true );
	$block_data = get_values_from_json_attr_keys( MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/posts/attributes.json', $attrs );
	
	$extra_args = array(
		'offset' => ( $page - 1 ) * $attrs['postCount'],
	);
	
	$posts = get_block_posts_data( $block_data, $extra_args );
	
	wp_send_json_success( $posts );
}
add_action( 'wp_ajax_mobiloud_block_posts_load_more', '\Blocks\block_posts_load_more' );
add_action( 'wp_ajax_nopriv_mobiloud_block_posts_load_more', '\Blocks\block_posts_load_more' );

function get_values_from_json_attr_keys( $path, $attrs = array() ) {
	global $wp_filesystem;
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();

	if ( empty( $path ) ) {
		return array();
	}

	if ( ! $wp_filesystem->exists( $path ) ) {
		return array();
	}

	$json_string = $wp_filesystem->get_contents( $path );
	$json_array  = json_decode( $json_string, true );
	$block_data  = array();

	foreach ( $json_array['front'] as $key => $item ) {
		switch ( $item['type'] ) {
			case 'array':
				$block_data[ $key ] = ml_get_block_attr( $attrs, $key, array() );
				break;
			case 'object':
				$block_data[ $key ] = ml_get_block_attr( $attrs, $key, new \ArrayObject() );
				break;
			default:
				$block_data[ $key ] = ml_get_block_attr( $attrs, $key, $item['default'] );
				break;
		}
	}

	foreach ( $json_array['block'] as $key => $item ) {
		switch ( $item['type'] ) {
			case 'array':
				$block_data[ $key ] = ml_get_block_attr( $attrs, $key, array() );
				break;
			case 'object':
				$block_data[ $key ] = ml_get_block_attr( $attrs, $key, new \ArrayObject() );
				break;
			default:
				$block_data[ $key ] = ml_get_block_attr( $attrs, $key, $item['default'] );
				break;
		}
	}

	$shared_attributes = ml_get_shared_attributes( $attrs );
	$block_data = array_merge( $block_data, $shared_attributes );

	return $block_data;
}

function get_ordered_product_ids_by_customer_id( $user_id = 0) {
	$data = array();

	$customer_orders = get_posts( array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => $user_id,
		'post_type'   => wc_get_order_types(),
		'post_status' => array_keys( wc_get_is_paid_statuses() ),
	) );

	if ( ! $customer_orders ) {
		$data['posts'] = array();
		return $data;
	}

	foreach ( $customer_orders as $customer_order ) {
		$order = wc_get_order( $customer_order->ID );
		$items = $order->get_items();

		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();
			$product_ids[] = $product_id;
		}
	}

	$product_ids = array_unique( $product_ids );

	return $product_ids;
}

class MobiLoud_Blocks_Posts_Rest_Endpoint extends \WP_REST_Controller {
	public function __construct() {
		$this->namespace     = 'ml-blocks/v1';
		$this->resource_name = 'posts';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->resource_name, array(
			array(
				'methods'   => 'GET',
				'callback'  => array( $this, 'get_posts' ),
				'permission_callback' => '__return_true',
			),
		) );
	}

	public function get_posts( $request ) {
		$params = $request->get_params();

		$posts = get_block_posts_data( $params );

		/**
		 * List of post types.
		 */
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'object',
			'and'
		);

		$posts['postTypes'] = $post_types;

		/**
		 * List of taxonomies attached to the post type.
		 */
		$taxonomies = get_object_taxonomies( $params['currentPostType'], 'objects' );
		$taxonomies = array_filter( $taxonomies, function( $taxonomy ) {
			return $taxonomy->public === true;
		} );

		foreach ( $taxonomies as $tax_slug => $tax_details ) {
			$terms = get_terms( array(
				'taxonomy'   => $tax_slug,
				'hide_empty' => false,
			) );

			$terms = array_map( function( $term ) {
				return array(
					'label' => $term->name,
					'value' => $term->term_id,
				);
			}, $terms );

			$taxonomies[ $tax_slug ]->terms = $terms;
		}

		$posts['taxonomies'] = $taxonomies;
		$posts['plugins'] = array(
			'woocommerce' => is_woocommerce_activated(),
		);

		return rest_ensure_response( $posts );
	}
}

function mobiloud_blocks_posts_rest_endpoint() {
	$controller = new MobiLoud_Blocks_Posts_Rest_Endpoint();
	$controller->register_routes();
}
add_action( 'rest_api_init', '\Blocks\mobiloud_blocks_posts_rest_endpoint' );

/**
 * Converts an associative array to inline CSS.
 *
 * @param array $array Associative array of CSS properties and values.
 *
 * @return string
 */
function mobiloud_assoc_array_to_css( $array = array() ) {
	$css_string = '';

	foreach ( $array as $prop => $value ) {
		$css_string .= $prop . ':' . $value . ';';
	}

	return $css_string;
}