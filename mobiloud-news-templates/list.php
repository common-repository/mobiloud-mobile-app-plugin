<?php Mobiloud::get_default_template_header(); ?>

<?php
	global $wp;
	$posts = Mobiloud::get_default_template_list_data();
	$toggleTitle = (boolean)Mobiloud::get_option( 'dt-list-title-toggle', true );
	$toggleAuthor = (boolean)Mobiloud::get_option( 'dt-list-author-toggle', true );
	$toggleCategory = (boolean)Mobiloud::get_option( 'dt-list-category-toggle', true );
	$toggleDate = (boolean)Mobiloud::get_option( 'dt-list-date-toggle', true );
	$toggleExcerpt = (boolean)Mobiloud::get_option( 'dt-list-content-toggle', true );
	$list_style = get_option( 'ml_article_list_view_type' );
	$ml_endpoint = isset( $wp->query_vars['__ml-api'] ) ? $wp->query_vars['__ml-api'] : '';
?>

<body>
	<ons-page>
		<?php if ( 'list' === $ml_endpoint ) : ?>
		<ons-pull-hook id="pull-to-refresh-list">
			Pull to refresh
		</ons-pull-hook>
		<?php endif; ?>
		<div class="post-list-default-template post-list<?php echo 'compact' === $list_style ? '' : '-expanded'; ?> post-list--divider-true">
			<?php foreach ( $posts['posts'] as $key => $post ) : ?>
				<?php require Mobiloud::get_default_template(); ?>
			<?php endforeach; ?>
		</div>

		<?php if ( 'list' === $ml_endpoint ) : ?>
		<div style="display: flex; justify-content: center;  padding: 1rem  0 0.5rem 0;">
			<ons-progress-circular indeterminate></ons-progress-circular>
		</div>
		<?php endif; ?>
	</ons-page>
</body>

<?php Mobiloud::get_default_template_footer(); ?>
