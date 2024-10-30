<?php
/**
 * This is a list template: regular_footer.php.
 *
 * We fill results using query parameters here and save it into JS variables.
 * Also all the JS code is here.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/list
 * @version 4.3.9
 */


// Get list of articles json.
$response = '{}';
if ( class_exists( 'MLApiController' ) ) {
	$api = new MLApiController();
	$api->set_error_handlers( ! empty( $debug ) ); // $debug defined in loop.php.

	$custom_response = apply_filters( 'mobiloud_custom_list_results', null );

	if ( ! empty( $custom_response ) ) {
		$response = $custom_response;
	} else {
		$response = $api->handle_request();
	}
}

$data = $response;

add_action( 'wp_print_footer_scripts', 'ml_loop_scripts', 300 );
add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 300 );
add_action( 'wp_footer', 'wp_print_footer_scripts', 200 );

do_action( 'wp_footer' );

/**
* Called before inline js block.
*/
do_action( 'mobiloud_custom_list_scripts_pre' );
$site_url = trailingslashit( get_bloginfo( 'url' ) );
if ( Mobiloud_Cache::is_api_enabled() ) {
	$site_url = Mobiloud_Cache::update_api_url( $site_url );
}
/**
* Filter to allow overriding default site url.
*
* @param string Current site url.
*
* @since 4.2.6
*/
$site_url = apply_filters( 'ml_list_site_url', $site_url );
?>
<script type="text/javascript" data-cfasync="false">
	var mlPostsData = <?php echo $data; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- this is a string containing json ?>  || {posts:[],'post-count':0};
	var mlFirstData = { posts:[] };
	for (var k in mlPostsData.posts) {
		if ( 'undefined' !== typeof( mlPostsData.posts[k].post_id ) ) {
			mlFirstData.posts.push( { post_id: mlPostsData.posts[k].post_id } );
		}
	}
	var ml_infinite_loaded = ( mlPostsData['posts'] || [] ).length - ( mlPostsData['ad_count'] || 0 ); // do not count Ads.
	var rendered = 0;
	var defaultThumb = <?php echo wp_json_encode( get_option( 'ml_default_featured_image', 'http://placehold.it/800x450' ) ); ?>;
	var siteURL = <?php echo wp_json_encode( $site_url ); ?>;

	var noMorePosts = false;


	document.addEventListener( "DOMContentLoaded", function( event ) {
		if ( ml_infinite_loaded === 0 ) {
			document.querySelector("body").innerHTML = '<h3 style="margin: 20px;">No posts found.</h3>';
			return;
		}

		if (!document.getElementById('article-list').classList.contains('rendered')) {
			renderList(mlPostsData);
		}

		document.querySelector("body").dispatchEvent(new Event('scroll'));

		var page = document.getElementById('load-more-page');

		page.onInfiniteScroll = function (done) {
			if (!noMorePosts) {
				noMorePosts = true;
				getNewArticles(siteURL, ml_infinite_loaded, <?php echo wp_json_encode( sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) ); ?>).then(function (more) {
					if (more === 0) {
						noMorePosts = true;
					} else {
						noMorePosts = false;
					}
					ml_infinite_loaded += more;
					document.getElementById('loading-more').style.display = 'none';
					done(); // Important!
				});
			} else {
				// end of posts animation / effect.
				document.getElementById('loading-more').style.display = 'none';
			}
		};

		// Pull hook.
		var pullHook = document.getElementById('pull-hook');

		if (ons.platform.isIOS()) {
			pullHook.classList.add('ios');
			document.body.classList.add('ml-ios');
		} else {
			pullHook.classList.add('android');
			document.body.classList.add('ml-android');
		}

		pullHook.addEventListener('changestate', function (event) {
			var message = '';

			switch (event.state) {
				case 'initial':
					message = 'Pull to refresh';
					break;
				case 'preaction':
					message = 'Release';
					break;
				case 'action':
					message = 'Loading...';
					break;
			}

			pullHook.innerHTML = message;
		});

		pullHook.onAction = function (done) {
			nativeFunctions.reloadWebview();
			setTimeout(done, 3000);
		};

	} );
</script>
<?php
// after main scripts block.
if ( Mobiloud::get_option( 'ml_list_ads_enabled' ) ) {
	$show_subscribed = Mobiloud::get_option( 'ml_list_ads_show_to_subscribed', false );
	$header_isset    = isset( $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'] ) && 'true' === $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'];
	if ( ! $header_isset || $header_isset && $show_subscribed ) {
		echo Mobiloud::get_option( 'ml_list_ads_static_content' ); // sanitize functions break possible js script content.
	}
}
