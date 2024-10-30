<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">
<?php
/**
 * This is a list template: regular_head.php.
 *
 * There is the <head>...</head> content.
 * It include code for styles and scripts.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/list
 * @version 4.2.0
 */

/**
 * Filters styles for the list.
 *
 * @var array Styles, associative array with styles.
 */
$styles = apply_filters(
	'mobiloud_list_show_styles',
	// Define custom styles for the list.
	array(
		'font-family-body'    => '"Roboto", "Open Sans", Arial, sans-serif',
		'font-family-heading' => '"Roboto", Arial, sans-serif',
		'font-size-body'      => '1rem',
		'font-size-heading'   => '15px',
		'color-text-body'     => '#555',
		'color-text-heading'  => '#000',
		'color-text-author'   => '#333',
		'color-text-category' => '#333',
		'font-family-meta'    => '"Roboto", "Open Sans", "Arial", sans-serif',
		'font-size-meta'      => '14px',
	)
);

/**
* Filters what fields to show inside main area and meta area.
* Please note:
* It is possible to use any custom fields.
* Please define (in javascript) custom function ml_render_list_fields( field_name, article_object ) and return a string.
*
* @see related note for "ml_render_list_fields" function in standard loop/js/loop.js file.
*
* @since 4.2.0
*
* @param string[] $fields Fields list.
*                       Supported standard fields list:
*                      'title':            Title.
*                      'date':             Date, just a date or a pretty date.
*                      'excerpt':          Excerpt.
*                      'category':         First category.
*                      'categories':       All categories.
*                      'author':           Author.
*                      'custom':           Custom field.
*                      'comments':         Number of comments.
*                      'category-author':  First category and author in single line.
* @param string   $area  Current area.
*                      Possible values: 'main', 'meta'.
*/
$main_order = apply_filters(
	'mobiloud_list_show_fields',
	[
		'title',
		'date',
		'excerpt',
	],
	'main'
);
/** This filter is documented above */
$meta_order = apply_filters(
	'mobiloud_list_show_fields',
	[
		'comments',
		'category',
		'author',
		'custom',
	],
	'meta'
);
// hide fields using options from Configuration/Settings tab/Article List settings.
$hidden_fields = [];
if ( ! Mobiloud::get_option( 'ml_article_list_enable_dates' ) ) {
	$hidden_fields[] = 'date';
}
if ( ! Mobiloud::get_option( 'ml_article_list_show_excerpt' ) ) {
	$hidden_fields[] = 'excerpt';
}
if ( ! Mobiloud::get_option( 'ml_article_list_show_comment_count' ) ) {
	$hidden_fields[] = 'comments';
}
if ( ! Mobiloud::get_option( 'ml_article_list_show_category' ) ) {
	$hidden_fields[] = 'category';
}
if ( ! Mobiloud::get_option( 'ml_article_list_show_author' ) ) {
	$hidden_fields[] = 'author';
}
if ( ! Mobiloud::get_option( 'ml_custom_field_enable' ) ) {
	$hidden_fields[] = 'custom';
} elseif ( '' === Mobiloud::get_option( 'ml_custom_field_name' ) ) {
	$hidden_fields[] = 'custom';
}
foreach ( $hidden_fields as $del_value ) {
	$key = array_search( $del_value, $main_order );
	if ( false !== $key ) {
		unset( $main_order[ $key ] );
	}
	$key = array_search( $del_value, $meta_order );
	if ( false !== $key ) {
		unset( $meta_order[ $key ] );
	}
}

/**
 * Register stylesheets for lists
 *
 * @global list_type
 */
function ml_list_stylesheets() {
	global $list_type;
	wp_enqueue_style( 'onsenui', MOBILOUD_PLUGIN_URL . 'libs/onsen/css/onsenui.min.css', [], MOBILOUD_PLUGIN_VERSION );
	wp_enqueue_style( 'onsen-components', MOBILOUD_PLUGIN_URL . 'libs/onsen/css/onsen-css-components.min.css', [], MOBILOUD_PLUGIN_VERSION );

	wp_enqueue_style( 'fonts-roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap', null, null );

	/**
	* Filter for loop.css url
	*
	* @param mixed
	*/
	$loop_css_url = apply_filters( 'mobiloud_list_css_custom', MOBILOUD_PLUGIN_URL . 'loop/css/loop.css', $list_type );
	wp_enqueue_style( 'mobiloud-list', $loop_css_url, [], MOBILOUD_PLUGIN_VERSION );
	/**
	* Called when styles enqueued at the loop page.
	*
	* @since 4.2.0
	*/
	do_action( 'mobiloud_list_enqueue_style' );
}

/**
 * Register scripts for lists
 *
 * @global list_type
 */
function ml_loop_scripts() {
	global $list_type;
	wp_enqueue_script( 'onsenui', MOBILOUD_PLUGIN_URL . 'libs/onsen/js/onsenui.min.js', array(), MOBILOUD_PLUGIN_VERSION, true );

	/**
	* Filter to allow overriding of loop.js file (article list)
	*
	* @param string Return custom loop.js url.
	* @param string $list_type List type: favorites, search, custom, regular.
	*/
	$loop_js_url = apply_filters( 'mobiloud_list_js_custom', MOBILOUD_PLUGIN_URL . 'loop/js/loop.js', $list_type );

	wp_enqueue_script( 'mobiloud-list', $loop_js_url, array( 'onsenui' ), MOBILOUD_PLUGIN_VERSION, true );
	/**
	* Called when scripts enqueued at the loop page.
	*
	* @since 4.2.0
	*/
	do_action( 'mobiloud_list_enqueue_script' );
}

remove_all_actions( 'wp_head' );
remove_all_actions( 'wp_footer' );
remove_all_actions( 'wp_print_styles' );
remove_all_actions( 'wp_enqueue_scripts' );
remove_all_actions( 'locale_stylesheet' );
remove_all_actions( 'wp_print_head_scripts' );
remove_all_actions( 'wp_print_footer_scripts' );
remove_all_actions( 'wp_shortlink_wp_head' );

add_action( 'wp_print_styles', 'ml_list_stylesheets' );

add_action( 'wp_head', 'wp_print_styles' );
add_action( 'wp_print_footer_scripts', 'ml_loop_scripts', 300 );
add_action( 'wp_print_footer_scripts', '_wp_footer_scripts', 300 );
add_action( 'wp_footer', 'wp_print_footer_scripts', 200 );

/**
 * Prepend "data-cfasync" parameter to scripts.
 *
 * @param string $tag
 * @param string $handle
 */
function ml_list_add_data_attribute( $tag, $handle ) {
	if ( in_array( $handle, array( 'onsenui', 'mobiloud-list' ), true ) ) {
		return str_replace( ' src', ' data-cfasync="false" src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'ml_list_add_data_attribute', 10, 2 );

wp_head();
?>

<style type="text/css">

	body,
	body p,
	.article-list__content p {
		font-family: <?php echo $styles['font-family-body']; ?>;
		font-size: <?php echo esc_attr( $styles['font-size-body'] ); ?>;
		color: <?php echo esc_attr( $styles['color-text-body'] ); ?>;
	}
	.article-list__content h2 {
		font-family: <?php echo $styles['font-family-heading']; ?>;
		font-size: <?php echo esc_attr( $styles['font-size-heading'] ); ?>;
		color: <?php echo esc_attr( $styles['color-text-heading'] ); ?>;
	}
	.article-list__meta {
		font-family: <?php echo $styles['font-family-meta']; ?>;
		font-size: <?php echo esc_attr( $styles['font-size-meta'] ); ?>;
	}
	.article-list__meta a.category, .article-list__meta_category, .article-list__meta_categories {
		color: <?php echo esc_attr( $styles['color-text-category'] ); ?>;
	}
	.article-list__meta .article-list__meta_author, .article-list__meta_comments {
		color: <?php echo esc_attr( $styles['color-text-author'] ); ?>;
	}
	<?php
	if ( get_option( 'ml_rtl_text_enable' ) ) {
		?>
		.page__content {
			direction: rtl;
		}
		<?php
	}
	?>
</style>
<?php
$ml_list = [
	'main_order'    => $main_order,
	'meta_order'    => $meta_order,
	'resize_images' => Mobiloud::get_option( 'ml_original_size_image_list', true ),
	'is_subscribed' => isset( $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'] ) && 'true' === $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'],
];
?>
<script type="text/javascript" data-cfasync="false">
	var ml_list = <?php echo wp_json_encode( $ml_list ); ?>;
</script>
<?php
$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

$custom_js = stripslashes( get_option( 'ml_post_custom_js' ) );
echo $custom_js ? '<script>' . $custom_js . '</script>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

eval( stripslashes( get_option( 'ml_post_head' ) ) ); // phpcs:ignore Squiz.PHP.Eval.Discouraged -- PHP in HEAD.
echo stripslashes( get_option( 'ml_html_post_head', '' ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- HTML in HEAD.
