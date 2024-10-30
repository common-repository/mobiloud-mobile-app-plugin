<?php Mobiloud::get_default_template_header(); ?>

<?php
	global $post;

	$post_id = htmlspecialchars( esc_attr( sanitize_text_field( $post->ID ) ) );
	$post    = get_post( $post_id );
	$title   = get_the_title( $post );
	$content = get_the_content( null, false, $post );
	$thumb   = get_the_post_thumbnail_url( $post );
?>

<body class="dt-body--post-page mb_body mb_article">
	<?php if ( ! empty( $thumb ) ) : ?>
		<div class="dt-pp__thumbnail" style="background: url( <?php echo esc_url( $thumb ); ?> )"></div>
	<?php endif; ?>
	<h1 class="dt-pp__title mb_post_title">
		<?php echo apply_filters( 'ml_post_title_escape_html', true ) ? esc_html( $title ) : $title; ?>
	</h1>

	<div class="dt-pp__content ml_post_content">
		<?php echo apply_filters( 'the_content', $content ); ?>
	</div>
</body>

<?php Mobiloud::get_default_template_footer(); ?>
