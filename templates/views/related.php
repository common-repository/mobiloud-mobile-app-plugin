<?php
/**
 * This is related posts template: related.php.
 *
 * Note: You can replace related posts' content by custom solution using 2 ways:
 * - (before the version 4.2.0) define custom function with name "ml_related_posts_js()" and do not replace the whole template.
 * - (starting from version 4.2.0) this template may be replaced by custom template of extension plugin. This is a recommended way.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/views
 * @version 4.2.0
 */

if ( ! function_exists( 'ml_related_posts_js' ) ) {
	function ml_related_posts_js( $post_id ) {
		?>
		<div id="ml_relatedposts" class="ml-relatedposts"></div>
		<?php
	}
}

// Call function for related items output with current post ID as parameter.
/** @var WP_Post $post */
ml_related_posts_js( $post->ID );
