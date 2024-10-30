
<?php Mobiloud::get_default_template_header(); ?>

<?php
	$post_id   = htmlspecialchars( esc_attr( sanitize_text_field( $_GET['post_id'] ) ) );
	$post      = get_post( $post_id );
	$author    = $post->post_author;
	$author    = get_the_author_meta( 'display_name', $author );
	$title     = get_the_title( $post );
	$date      = get_the_date( '', $post );
	$content   = get_the_content( null, false, $post );
	$thumb     = get_the_post_thumbnail_url( $post );
	?>

<?php
	do_action( 'mobiloud_before_content_requests' );
	$pc_filter = apply_filters( 'ml_default_post_content_filter', true );
?>
<body class="dt-body--post-page mb_body mb_article" style="overflow: <?php echo $pc_filter ? 'scroll' : 'hidden' ?>">
	<?php
		echo wp_kses( stripslashes( get_option( 'ml_html_post_start_body', '' ) ), Mobiloud::expanded_alowed_tags() );
		eval( stripslashes( get_option( 'ml_post_before_details' ) ) ); // phpcs:ignore Squiz.PHP.Eval.Discouraged
		echo wp_kses( stripslashes( get_option( 'ml_html_post_before_details', '' ) ), Mobiloud::expanded_alowed_tags() );
	?>
	<?php if ( ! empty( $thumb ) ) : ?>
		<div class="dt-pp__thumbnail" style="background: url( <?php echo esc_url( $thumb ); ?> )"></div>
	<?php endif; ?>
	<h1 class="dt-pp__title mb_post_title">
		<?php echo apply_filters( 'ml_post_title_escape_html', true ) ? esc_html( $title ) : $title; ?>
	</h1>

	<div class="dt-pp__date mb_post_date">
		<?php echo esc_html( $date ); ?>
	</div>

	<?php if ( ! empty( $author ) ) : ?>
		<div class="dt-pp__author mb_post_meta">
			<?php echo esc_html( $author ); ?>
		</div>
	<?php endif; ?>

	<div class="dt-pp__content ml_post_content">
		<?php echo apply_filters( 'the_content', $content ); ?>
	</div>
</body>

<?php Mobiloud::get_default_template_footer(); ?>
