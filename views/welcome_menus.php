<!-- step 4 -->
<?php
$next_step  = add_query_arg( 'step', Mobiloud_Admin::$welcome_steps[3], remove_query_arg( [ 'step', 'tab' ] ) );
$categories = get_categories(
	[
		'hide_empty' => false,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 100,
	]
);
$top_count  = 20;
// first 20 categories.
$first = array_splice( $categories, 0, $top_count );
usort(
	$first, function( $a, $b ) {
		return strcmp( strtolower( $a->name ), strtolower( $b->name ) );
	}
);
usort(
	$categories, function( $a, $b ) {
		return strcmp( strtolower( $a->name ), strtolower( $b->name ) );
	}
);

function output_categories( $categories, $checked_and_visible = true ) {
	foreach ( $categories as $cat ) {
		$id = "cat_{$cat->term_id}";
		?>
		<div class="ml-choice-cat ml-choice-wrap
		<?php
		if ( ! $checked_and_visible ) {

			?>
		 hidden-cat
			<?php
		}
		?>
		" data-name="<?php echo esc_attr( $cat->name ); ?>">
			<input type="checkbox" name="ml_cat[]" value="<?php echo esc_attr( $cat->term_id ); ?>" id="<?php echo esc_attr( $id ); ?>" <?php checked( $checked_and_visible ); ?>>
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $cat->name ); ?></label>
		</div>
		<?php
	}

}

?>
<div class="ml2-block ml2-welcome-block">
	<div class="ml2-body text-left">
		<form action="<?php echo esc_attr( $next_step ); ?>" method="post" class="contact-form">
			<?php wp_nonce_field( 'ml-form-welcome' ); ?>
			<input type="hidden" name="step" value="4">
			<h3 class="title-main">Which are the main categories on your website?</h3>
			<p class="title-description">We'll make these front and center on your app.</p>
			<div class="categories-choice">
				<div class="cat-column" id="column_0">
					<?php
					$n = intval( ceil( count( $first ) / 2 ) );
					output_categories( array_slice( $first, 0, $n ) );
					?>
				</div>
				<div class="cat-column" id="column_1">
					<?php
					output_categories( array_slice( $first, $n ) );
					output_categories( $categories, false );
					?>
				</div>
				<div class="break"></div>
				<div class="cat-column" id="column_2">
					<?php
					if ( count( $categories ) ) {
						?>
						<div class="outer">
							<div class="middle">
								<div class="inner">
									<a class="cat-load-more" href="#">Load more</a>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<div class="cat-column" id="column_3"></div>
			</div>
			<br>
			<?php
			// Get all registered nav menus.
			$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
			if ( count( $menus ) ) {
				?>
				<h3 class="title-main">Which is your main menu?</h3>
				<p class="title-description">This is usually what you're showing in your site's header or hamburger menu.</p>
				<div class="menu-choice">
					<select name="ml_menu_nav" class="ml-select">
						<?php
						$selected = Mobiloud::get_option( 'ml_hamburger_nav', '' );
						foreach ( $menus as $menu ) {
							echo "<option value='" . esc_attr( $menu->slug ) . "' " . selected( $selected, $menu->slug, false ) . '>' . esc_html( $menu->name ) . '</option>';
						}
						?>
					</select>
				</div>
				<?php
			}
			$pages = get_posts(
				[
					'post_type'   => 'page',
					'post_status' => 'publish',
					'orderby'     => 'name',
					'order'       => 'ASC',
					'numberposts' => 1000,
				]
			);
			if ( $pages ) {
				?>
				<h3 class="title-main">Which are the additional pages to show in the app?</h3>
				<p class="title-description">These will be displayed in a separate area of the app, organized, for a good user experience<p>
				<div class="checkboxes-wrap">
					<?php
					foreach ( $pages as $page ) {
						$id    = $page->ID;
						$title = get_the_title( $id );
						if ( '' === $title ) {
							$title = __( '(no title)' );
						}
						?>
						<div class="ml-choice-page ml-choice-wrap">
							<input type="checkbox" name="ml_add[]" value="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( 'pages_add_' . $id ); ?>">
							<label for="<?php echo esc_attr( 'pages_add_' . $id ); ?>"><?php echo esc_html( $title ); ?></label>
						</div>
						<?php
					}
					?>
				</div>
				<br>
				<h3 class="title-main">Which are the pages with terms and conditions and privacy policy?</h3>
				<p class="title-description">These will be displayed in your app settings</p>
				<div class="checkboxes-wrap">
					<?php
					foreach ( $pages as $page ) {
						$id    = $page->ID;
						$title = get_the_title( $id );
						if ( '' === $title ) {
							$title = __( '(no title)' );
						}
						?>
						<div class="ml-choice-page ml-choice-wrap">
							<input type="checkbox" name="ml_terms[]" value="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( 'pages_terms_' . $id ); ?>">
							<label for="<?php echo esc_attr( 'pages_terms_' . $id ); ?>"><?php echo esc_html( $title ); ?></label>
						</div>
						<?php
					}
					?>
				</div>
				<br>
				<?php
			}

			?>
			<div class='ml-col-row ml-init-button'>
				<button type="submit" name="submit" id="submit" class="button button-hero button-primary ladda-button" data-style="zoom-out">Save and Continue</button>
			</div>
		</form>

	</div>
</div>
