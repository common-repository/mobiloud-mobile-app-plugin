<!DOCTYPE html>
<html dir="<?php echo( get_option( 'ml_rtl_text_enable' ) ? 'rtl' : 'ltr' ); ?>">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="language" content="en"/>
		<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Roboto+Slab:wght@100;200;300;400;500;600;700;800;900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

		<?php
			global $post;
			global $wp;
			$action_filter_status = false;

			$page_type = isset( $wp->query_vars['__ml-api'] ) ? $wp->query_vars['__ml-api'] : $wp->query_vars['post_type'];

			if ( null !== $post ) {
				$action_filter_status = (boolean)Mobiloud::get_action_filter_status( $post->ID );
			}

			$disable_hooks = ( 'list' === $page_type || ( 'app-pages' === $page_type && $action_filter_status ) );

			if ( ! $disable_hooks ) {
				remove_all_actions( 'wp_head' );
				remove_all_actions( 'wp_footer' );
				remove_all_actions( 'wp_print_styles' );
				remove_all_actions( 'wp_enqueue_scripts' );
				remove_all_actions( 'locale_stylesheet' );
				remove_all_actions( 'wp_print_head_scripts' );
				remove_all_actions( 'wp_print_footer_scripts' );
				remove_all_actions( 'wp_shortlink_wp_head' );
			}
		?>

		<?php
			$prefix = '';

			switch ( $page_type ) {
				case 'list':
					$prefix = 'dt-list-';
					break;
				case 'post':
				case 'app-pages':
					$prefix = 'dt-post-page-';
					break;
				default:
					break;
			}
		?>

		<?php if ( 'list' === $page_type ) : ?>
			<link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsenui.css">
			<link rel="stylesheet" href="https://unpkg.com/onsenui/css/onsen-css-components.min.css">
			<link rel="stylesheet" href="<?php echo MOBILOUD_PLUGIN_URL . 'blocks/build/style-index.css'; ?>" />
		<?php endif; ?>

		<?php if ( 'post' === $page_type || 'app-pages' === $page_type ) : ?>
			<link rel="stylesheet" href="<?php echo MOBILOUD_PLUGIN_URL . '/build/post.css' ?>">
		<?php endif; ?>

		<?php
			// Title.
			$title_font = Mobiloud::get_option( $prefix . 'title-font', 'Roboto' );
			$title_font_size = Mobiloud::get_option( $prefix . 'title-font-size', 1.8 );
			$title_line_height = Mobiloud::get_option( $prefix . 'title-line-height', 1.3 );
			$title_color = Mobiloud::get_option( $prefix . 'title-color', '#000' );

			// Author.
			$author_font = Mobiloud::get_option( $prefix . 'author-font', 'Roboto' );
			$author_font_size = Mobiloud::get_option( $prefix . 'author-font-size', 0.6785 );
			$author_line_height = Mobiloud::get_option( $prefix . 'author-line-height', 0.92 );
			$author_color = Mobiloud::get_option( $prefix . 'author-color', '#000' );

			// Category.
			$category_font = Mobiloud::get_option( $prefix . 'category-font', 'Roboto' );
			$category_font_size = Mobiloud::get_option( $prefix . 'category-font-size', 0.6785 );
			$category_line_height = Mobiloud::get_option( $prefix . 'category-line-height', 0.92 );
			$category_color = Mobiloud::get_option( $prefix . 'category-color', '#000' );

			// Date.
			$date_font = Mobiloud::get_option( $prefix . 'date-font', 'Roboto' );
			$date_font_size = Mobiloud::get_option( $prefix . 'date-font-size', 0.6785 );
			$date_line_height = Mobiloud::get_option( $prefix . 'date-line-height', 0.92 );
			$date_color = Mobiloud::get_option( $prefix . 'date-color', '#000' );

			// Content.
			$content_font = Mobiloud::get_option( $prefix . 'content-font', 'Roboto' );
			$content_font_size = Mobiloud::get_option( $prefix . 'content-font-size', 0.85 );
			$content_line_height = Mobiloud::get_option( $prefix . 'content-line-height', 1.2 );
			$content_color = Mobiloud::get_option( $prefix . 'content-color', '#000' );
		?>

		<?php if ( 'list' === $page_type ) : ?>
			<style>
				.post-list-default-template .post-item__title {
					font-family: '<?php echo esc_html( $title_font ); ?>';
					font-size: <?php echo esc_html( $title_font_size ); ?>rem;
					font-weight: 500;
					line-height: <?php echo esc_html( $title_line_height ); ?>;
					color: <?php echo $title_color; ?>;
					margin-top: 8px;
				}

				.post-list-default-template .post-list__item-author {
					font-family: '<?php echo esc_html( $author_font ); ?>';
					font-size: <?php echo esc_html( $author_font_size ); ?>rem;
					line-height: <?php echo esc_html( $author_line_height ); ?>;
					color: <?php echo $author_color; ?>;
					margin-bottom: 5px;
				}

				.post-list-default-template .post-item__taxonomies {
					font-family: '<?php echo esc_html( $category_font ); ?>';
					font-size: <?php echo esc_html( $category_font_size ); ?>rem;
					line-height: <?php echo esc_html( $category_line_height ); ?>;
					color: <?php echo $category_color; ?>;
				}

				.post-list-default-template .post-list__item-date {
					font-family: '<?php echo esc_html( $date_font ); ?>';
					font-size: <?php echo esc_html( $date_font_size ); ?>rem;
					line-height: <?php echo esc_html( $date_line_height ); ?>;
					color: <?php echo $date_color; ?>;
				}

				.post-list-default-template .post-item__excerpt {
					font-family: '<?php echo esc_html( $content_font ); ?>';
					font-size: <?php echo esc_html( $content_font_size ); ?>rem;
					line-height: <?php echo esc_html( $content_line_height ); ?>;
					color: <?php echo $content_color; ?>;
				}
			</style>
		<?php endif; ?>

		<?php if ( 'post' === $page_type || 'app-pages' === $page_type ) : ?>
			<style>
				.dt-body--post-page .dt-pp__title {
					font-family: '<?php echo esc_html( $title_font ); ?>';
					font-size: <?php echo esc_html( $title_font_size ); ?>rem;
					font-weight: 500;
					line-height: <?php echo esc_html( $title_line_height ); ?>;
					color: <?php echo $title_color; ?>;
				}

				.dt-body--post-page .dt-pp__author {
					font-family: '<?php echo esc_html( $author_font ); ?>';
					font-size: <?php echo esc_html( $author_font_size ); ?>rem;
					line-height: <?php echo esc_html( $author_line_height ); ?>;
					color: <?php echo $author_color; ?>;
				}

				.dt-body--post-page .dt-list__content {
					font-family: '<?php echo esc_html( $content_font ); ?>';
					font-size: <?php echo esc_html( $content_font_size ); ?>rem;
					line-height: <?php echo esc_html( $content_line_height ); ?>;
					color: <?php echo $content_color; ?>;
				}
			</style>
		<?php endif; ?>

		<?php
		$custom_css = stripslashes( get_option( 'ml_post_custom_css' ) );
		echo $custom_css ? '<style type="text/css" media="screen">' . $custom_css . '</style>' : '';

		$custom_js = stripslashes( get_option( 'ml_post_custom_js' ) );
		echo $custom_js ? '<script>' . $custom_js . '</script>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		echo stripslashes( get_option( 'ml_html_post_head', '' ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- HTML in HEAD.
		eval( stripslashes( get_option( 'ml_post_head' ) ) ); // phpcs:ignore Squiz.PHP.Eval.Discouraged -- PHP in HEAD.

		?>

		<?php wp_head(); ?>
	</head>

	<body>