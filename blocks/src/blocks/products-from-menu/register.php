<?php
namespace Blocks;

class Products_from_menu extends \Blocks\Setup {
	public function __construct() {
		$this->block_name = 'products-from-menu';
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
		$block_data     = get_values_from_json_attr_keys( $this->attr_file_path, $attrs );
		$data           = get_products_from_menu( (int)$block_data['menuId'] );
		$posts['posts'] = $data['productArray'];
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

( new Products_from_menu() )->init();

class MobiLoud_Blocks_Products_From_Menu_Rest_Endpoint extends \WP_REST_Controller {
	public function __construct() {
		$this->namespace     = 'ml-blocks/v1';
		$this->resource_name = 'products_from_menu';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->resource_name, array(
			array(
				'methods'   => 'GET',
				'callback'  => array( $this, 'get_products_from_menu' ),
				'permission_callback' => '__return_true',
			),
		) );
	}

	public function get_products_from_menu( $request ) {
		$params = $request->get_params();
		$menu_id = (int)$params['menuId'];
		$data = get_products_from_menu( $menu_id );

		$data['plugins'] = array(
			'woocommerce' => is_woocommerce_activated(),
		);

		return rest_ensure_response( $data );
	}
}

function mobiloud_blocks_products_from_menu_rest_endpoint() {
	$controller = new MobiLoud_Blocks_Products_From_Menu_Rest_Endpoint();
	$controller->register_routes();
}
add_action( 'rest_api_init', '\Blocks\mobiloud_blocks_products_from_menu_rest_endpoint' );
