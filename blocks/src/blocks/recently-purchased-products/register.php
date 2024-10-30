<?php
namespace Blocks;

class Recently_purchased_products extends \Blocks\Setup {
	public function __construct() {
		$this->block_name = 'recently-purchased-products';
		$this->block_name_ns = $this->namespace . '/' . $this->block_name;
		$this->attr_file_path = MOBILOUD_PLUGIN_DIR . 'blocks/src/blocks/' . $this->block_name . '/attributes.json';
	}

	public function init() {
		add_action( 'init', array( $this, 'register_current_block' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_block_scripts' ) );
	}

	public function register_current_block() {
		$this->register_block( $this->block_name_ns );
	}

	public function generate_data( $attrs ) {
		$user_id     = get_current_user_id();
		$block_data  = get_values_from_json_attr_keys( $this->attr_file_path, $attrs );
		$product_ids = get_ordered_product_ids_by_customer_id( $user_id );
		$posts       = get_block_posts_data( array_merge(
			$block_data,
			array(
				'currentPostType' => 'product',
				'postIds'         => $product_ids,
			)
		) );
		?>
		<script>
			var ml_block_data = window.ml_block_data || [];
			ml_block_data.push( {
				blockName: '<?php echo $this->block_name_ns; ?>',
				blockData: {
					attrs: JSON.parse( JSON.stringify( <?php echo wp_json_encode( $block_data ); ?> ) ),
					posts: JSON.parse( JSON.stringify( <?php echo wp_json_encode( $posts ); ?> ) ),
				},
			} );
		</script>
		<?php
	}
}

( new Recently_purchased_products() )->init();

function mobiloud_get_block_recently_purchased_products_attrs( $attrs ) {
	$block_data = array();

	$block_data['colsMobile'] = ml_get_block_attr( $attrs, 'colsMobile', 2 );
	$block_data['colsTablet'] = ml_get_block_attr( $attrs, 'colsTablet', 3 );
	$block_data['currentPostType'] = ml_get_block_attr( $attrs, 'currentPostType', 'product' );
	$block_data['displayAs'] = ml_get_block_attr( $attrs, 'displayAs', 'list' );
	$block_data['moduleTitle'] = ml_get_block_attr( $attrs, 'moduleTitle', 'list' );
	$block_data['showAuthor'] = ml_get_block_attr( $attrs, 'showAuthor', true );
	$block_data['showDate'] = ml_get_block_attr( $attrs, 'showDate', true );
	$block_data['showFeaturedImage'] = ml_get_block_attr( $attrs, 'showFeaturedImage', true );
	$block_data['showPrice'] = ml_get_block_attr( $attrs, 'showPrice', true );

	return $block_data;
}

class MobiLoud_Blocks_Recently_Purchased_Products_Rest_Endpoint extends \WP_REST_Controller {
	public function __construct() {
		$this->namespace     = 'ml-blocks/v1';
		$this->resource_name = 'recently_purchased_products';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->resource_name, array(
			array(
				'methods'   => 'GET',
				'callback'  => array( $this, 'get_recently_purchased_products' ),
				'permission_callback' => '__return_true',
			),
		) );
	}

	public function get_recently_purchased_products( $request ) {
		$params = $request->get_params();
		$user_id = (int)$params['userId'];

		$data = array();

		$product_ids = get_ordered_product_ids_by_customer_id( $user_id );

		$data = get_block_posts_data( array(
			'currentPostType' => 'product',
			'postIds'         => $product_ids,
		) );

		$data['plugins'] = array(
			'woocommerce' => is_woocommerce_activated(),
		);

		return rest_ensure_response( $data );
	}
}

function blocks_recently_purchased_products_rest_endpoint() {
	$controller = new MobiLoud_Blocks_Recently_Purchased_Products_Rest_Endpoint();
	$controller->register_routes();
}
add_action( 'rest_api_init', '\Blocks\blocks_recently_purchased_products_rest_endpoint' );
