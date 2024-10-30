<?php
namespace Blocks;
require_once 'utility.php';

class Setup {
	public $namespace = 'mobiloud';
	public $block_name = '';
	public $block_name_ns = '';
	public $attr_file_path = '';

	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'mobiloud_block_header', array( $this, 'register_block_data_generator' ) );
	}

	public function register_block_data_generator() {
		$blocks = parse_blocks( get_the_content() );

		array_walk( $blocks, function( $value, $key ) {
			$block_name_ns = $value['blockName'];

			if ( empty( $block_name_ns ) ) {
				return;
			}

			$block_name_exploded = explode( '/', $block_name_ns );

			if ( 'mobiloud' !== $block_name_exploded[0] ) {
				return;
			}

			$block_name = str_replace( '-', '_', $block_name_exploded[1] );

			$block_data_generator_fn = "mobiloud_block_data_generator_{$block_name}";
			$class_name = '\Blocks\\' . ucfirst( $block_name );
			$instance = new $class_name();
			$block_data_generator_fn = 'generate_data';

			if ( ! method_exists( $instance, $block_data_generator_fn ) ) {
				return;
			}

			call_user_func( array( $instance, $block_data_generator_fn ) , $value['attrs'] );
		} );
	}

	public function register_block_scripts() {
		$screen = get_current_screen();
		$post_type = get_post_type();

		if ( $screen && 'post' !== $screen->base ) {
			return;
		}

		if ( ! ( 'list-builder' === $post_type || 'app-pages' === $post_type ) ) {
			return;
		}

		$script_asset_path = MOBILOUD_PLUGIN_DIR . 'blocks/build/index.asset.php';
		$index_js     = 'blocks/build/index.js';
		$script_asset = require( $script_asset_path );

		wp_register_script(
			'mobiloud-posts-block-editor',
			MOBILOUD_PLUGIN_URL . "$index_js",
			$script_asset['dependencies'],
			$script_asset['version']
		);

		$editor_css = 'blocks/build/index.css';
		wp_register_style(
			'mobiloud-posts-block-editor-style',
			MOBILOUD_PLUGIN_URL . "$editor_css",
			array(),
			filemtime( MOBILOUD_PLUGIN_DIR . "$editor_css" )
		);

		$style_css = 'blocks/build/style-index.css';

		wp_register_style(
			'mobiloud-posts-block',
			MOBILOUD_PLUGIN_URL . "$style_css",
			array(),
			filemtime( MOBILOUD_PLUGIN_DIR . "$style_css" )
		);
	}

	public function register_block( $block_name_ns, $instance = null ) {
		register_block_type(
			$block_name_ns,
			array(
				'editor_script' => 'mobiloud-posts-block-editor',
				'render_callback' => array( $instance, 'dynamic_block' ),
			)
		);
	}
}

new \Blocks\Setup();
