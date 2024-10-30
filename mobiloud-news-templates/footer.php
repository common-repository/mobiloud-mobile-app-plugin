	</body>
	<footer>
		<script src="https://unpkg.com/onsenui/js/onsenui.min.js"></script>
		<?php
			global $wp;
			$page_type = isset( $wp->query_vars['__ml-api'] ) ? $wp->query_vars['__ml-api'] : $wp->query_vars['post_type'];
		?>

		<?php if ( 'list' === $page_type ) : ?>
			<script>
				var ml_site_url = '<?php echo get_site_url(); ?>';
				var pppage = <?php echo Mobiloud::get_option( 'ml_articles_per_request', 15 ); ?>;
				var is_subscribed = '<?php echo isset( $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED'] ) && 'true' === $_SERVER['HTTP_X_ML_IS_USER_SUBSCRIBED']; ?>';
			</script>
			<script src="<?php echo esc_url( MOBILOUD_PLUGIN_URL . '/build/default-template.js' ); ?>"></script>
		<?php endif; ?>

		<?php
			wp_footer();
			eval( stripslashes( get_option( 'ml_post_footer' ) ) );
		?>
	</footer>
</html>
