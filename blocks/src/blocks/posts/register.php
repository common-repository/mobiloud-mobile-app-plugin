<?php
namespace Blocks;

class Posts extends \Blocks\Setup {
	public function __construct() {
		$this->block_name = 'posts';
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
		$block_data = get_values_from_json_attr_keys( $this->attr_file_path, $attrs );
		$posts = get_block_posts_data( $block_data );
		?>
		<script>
			var ml_block_data = window.ml_block_data || [];
			ml_block_data.push( {
				blockName: '<?php echo $this->block_name_ns; ?>',
				blockData: {
					attrs: JSON.stringify( <?php echo wp_json_encode( $block_data ); ?> ),
					posts: JSON.stringify( <?php echo wp_json_encode( $posts ); ?> ),
				},
			} );
		</script>
		<?php
	}
}

( new Posts() )->init();
