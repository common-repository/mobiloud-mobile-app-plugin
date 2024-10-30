<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sketch for the Rest API
 */

class MLRestAPI {
	private $debug;

	public function __construct() {
		$this->debug = false;

		include_once MOBILOUD_PLUGIN_DIR . '/api/controllers/MLApiController.php';
		add_action( 'rest_api_init', array( $this, 'ml_rest_api' ) );
	}

	public function ml_rest_api() {
		$namespace = 'ml-api/v2';

		register_rest_route(
			$namespace,
			'/version/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'version' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/config/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'config' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/menu/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'menu' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/login/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'login' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/comments/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'comments' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/comments/disqus/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'disqus' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/page/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'page' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/post/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'post' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$namespace,
			'/posts/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'posts' ),
				'permission_callback' => '__return_true',
			)
		);
	}


	public function posts() {
		include_once MOBILOUD_PLUGIN_DIR . '/api/controllers/MLApiController.php';
		$api = new MLApiController();
		$api->set_error_handlers( $this->debug );

		$response_data = $api->handle_request( true );
		$response      = new WP_REST_Response( $response_data );

		return $response;
	}

	public function version() {
		ob_start();
		include_once MOBILOUD_PLUGIN_DIR . 'version.php';
		$html_content = ob_get_clean();
		$data         = json_decode( $html_content );

		return $data;
	}

	public function config() {
		ob_start();
		include_once MOBILOUD_PLUGIN_DIR . 'config.php';
		$html_content = ob_get_clean();
		$data         = json_decode( $html_content );

		return $data;
	}

	public function login() {
		ob_start();
		include_once MOBILOUD_PLUGIN_DIR . '/subscriptions/login.php';
		$html_content = ob_get_clean();
		$data         = json_decode( $html_content );

		return $data;
	}

	public function menu() {
		ob_start();
		include_once MOBILOUD_PLUGIN_DIR . 'get_categories.php';
		$html_content = ob_get_clean();
		$data         = json_decode( $html_content );

		return $data;
	}

	public function post() {
		// include(MOBILOUD_PLUGIN_DIR . "post/post.php");
		return 'Api v2. Post. Not implemented. 200 OK';
	}


	public function comments() {
		// include_once MOBILOUD_PLUGIN_DIR . 'comments.php';
		return 'Api v2. Comments. Not implemented. 200 OK';
	}

	public function disqus() {
		return 'Api v2. Disqus comments. Not implemented. 200 OK';
	}

	public function page() {
		// include_once MOBILOUD_PLUGIN_DIR . 'get_page.php';
		return 'Api v2. Page. Not implemented. 200 OK';
	}

}
