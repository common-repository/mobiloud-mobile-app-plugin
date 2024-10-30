<?php
	$on_click = Mobiloud::get_native_link( $post['post_type'], $post['post_id'] );
	$excerpt_length = Mobiloud::get_option( 'dt-list-content-excerpt-length', 30 );
?>
<div class="post-list<?php echo 'compact' === $list_style ? '' : '-expanded'; ?>__item post-list__item--highlight-false" onclick="<?php echo $on_click; ?>">
	<ons-ripple color="rgba( 0, 0, 0, 0.15 )" background="rgba( 0, 0, 0, 0.15 )"></ons-ripple>
	<div class="post-list<?php echo 'compact' === $list_style ? '' : '-expanded'; ?>__item-thumbnail-wrapper post-list__item-thumbnail-wrapper--">
		<?php if ( isset( $post['images'] ) && count( $post['images'] ) > 0 ) : ?>
			<?php $img_url = is_array( $post['images'][0]['big-thumb'] ) ? $post['images'][0]['big-thumb']['url'] : $post['images'][0]['big-thumb'] ?>
			<img class="post-list__item-thumbnail" src="<?php echo esc_url( $img_url ); ?>" />
		<?php endif; ?>
	</div>
	<div class="post-list__item-text-wrapper">

		<!-- Title. -->
		<?php if ( $toggleTitle ) : ?>
			<div class="post-item__title">
				<?php echo esc_html( $post['title'] ); ?>
			</div>
		<?php endif; ?>

		<div class="post-item__meta post-item__date-author">

			<!-- Author -->
			<?php if ( $toggleAuthor && isset( $post['author'] ) && is_array( $post['author'] ) && isset( $post['author']['name'] ) ) : ?>
				<div class="post-list__item-author">
					<?php echo esc_html( $post['author']['name'] ); ?>
				</div>
			<?php endif; ?>

			<!-- Date -->
			<?php if ( $toggleDate ) : ?>
				<div class="post-list__item-date">
					<?php
						$date_format = get_option( 'date_format' );
						echo esc_html( date_i18n( $date_format, strtotime( $post['date'] ) ) );
					?>
				</div>
			<?php endif; ?>

		</div>

		<!-- Excerpt -->
		<?php if ( $toggleExcerpt && isset( $post['excerpt'] ) ) : ?>
			<div class="post-item__body post-item__excerpt">
				<?php echo esc_html( wp_trim_words( $post['excerpt'], $excerpt_length ) ); ?>
			</div>
		<?php endif; ?>

		<!-- Taxonomy -->
		<?php if ( $toggleCategory && isset( $post['categories'] ) && count( $post['categories'] ) > 0 ) : ?>
			<div class="post-item__meta post-item__taxonomies">
				<div class="post-list__item-taxonomy-row">
					<span class="post-list__item-taxonomy-type">Categories: </span><span class="post-list__item-taxonomy-term"><?php echo esc_html( $post['categories'][0]['name'] ); ?></span>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
