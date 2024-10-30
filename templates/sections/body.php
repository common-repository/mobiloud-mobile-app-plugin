<?php
/**
 * This is a sections template: body.php.
 *
 * It include all the <body>...</body> content.
 * It used by sections.php template.
 *
 * @package MobiLoud.
 * @subpackage MobiLoud/templates/sections
 * @version 4.2.0
 */

?>
<ons-page id="load-more-page">

	<ons-list id="sections-placeholder" class="sections-menu">
		<?php
		for ( $i = 0; $i <= 18; $i++ ) {
			echo '<ons-list-item class="is-placeholder"></ons-list-item>';
		}
		?>
	</ons-list>

	<?php
	if ( ! empty( $sections_menu ) ) {
		$menu = wp_get_nav_menu_items( $sections_menu );
		?>

		<ons-list id="sections-menu" class="sections-menu">

			<?php

			do_action( 'mobiloud_above_sections' );

			$article_list = 'list';

			foreach ( $menu as $item ) {
				if ( $item->menu_item_parent !== '0' ) {
					continue;
				}

				// This function defined in parent template.
				$children = getMenuItemsForParent( $sections_menu, $item->ID );

				$item_data  = ' data-ml-item-type="' . esc_attr( $item->type ) . '"';
				$item_data .= ' data-ml-item-object="' . esc_attr( $item->object ) . '"';
				$item_data .= ' data-ml-object-id="' . esc_attr( $item->object_id ) . '"';

				if ( empty( $item->opening_method ) ) {
					$item->opening_method = 'native';
				}

				$onclick = 'nativeFunctions.handlePost( ' . $item->object_id . ' )';

				if ( $item->type === 'post_type' && $item->opening_method !== 'native' ) {
					$onclick = "nativeFunctions.handleLink( '" . esc_url( $item->url ) . "', '" . esc_attr( $item->title ) . "', '" . esc_attr( $item->opening_method ) . "' )";
				}

				if ( $item->type === 'taxonomy' ) {
					$onclick = "nativeFunctions.handleLink( '" . esc_url( trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/' . $article_list . '?taxonomy=' . $item->object . '&term_id=' . $item->object_id ) . "', '" . esc_attr( $item->title ) . "', '" . esc_attr( $item->opening_method ) . "' )";
				}

				if ( $item->type === 'custom' ) {
					if ( $item->url !== '#' ) {
						$onclick = "nativeFunctions.handleLink( '" . esc_url( $item->url ) . "', '" . esc_attr( $item->title ) . "', '" . esc_attr( $item->opening_method ) . "' )";
					} else {
						$onclick = '';
					}
				}

				?>

				<ons-list-item tappable
					<?php
					if ( ! empty( $children ) ) {
						echo ' expandable' . $item_data; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
					} else {
						echo ' data-click="' . $onclick . '"' . $item_data;
					}
					?>
					>
					<?php echo esc_html( $item->title ); ?>
					<?php
					if ( ! empty( $children ) ) {

						echo '<div class="expandable-content">';

						foreach ( $children as $child ) {

							$item_data  = ' data-ml-item-type="' . esc_attr( $child->type ) . '"';
							$item_data .= ' data-ml-item-object="' . esc_attr( $child->object ) . '"';
							$item_data .= ' data-ml-object-id="' . esc_attr( $child->object_id ) . '"';

							$onclick = 'nativeFunctions.handlePost( ' . $child->object_id . ' )';

							if ( $child->type === 'post_type' && $child->opening_method !== 'native' ) {
								$onclick = "nativeFunctions.handleLink( '" . esc_url( $child->url ) . "', '" . esc_attr( $child->name ) . "', '" . esc_attr( $child->opening_method ) . "' )";
							}

							if ( $child->type === 'taxonomy' ) {
								$onclick = "nativeFunctions.handleLink( '" . esc_url( trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/' . $article_list . '?taxonomy=' . $child->object . '&term_id=' . $child->object_id ) . "', '" . esc_attr( $child->name ) . "', '" . esc_attr( $child->opening_method ) . "' )";
							}

							if ( $child->type === 'custom' ) {
								if ( $child->url !== '#' ) {
									$onclick = "nativeFunctions.handleLink( '" . esc_url( $child->url ) . "', '" . esc_attr( $child->title ) . "', '" . esc_attr( $child->opening_method ) . "' )";
								} else {
									$onclick = '';
								}
							}

							?>

							<ons-list-item data-click="<?php echo $onclick; ?>" tappable 
																  <?php
																	echo $item_data; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
																	if ( ! empty( $child->children ) ) {
																		echo ' expandable'; }
																	?>
								>
								<?php
								echo esc_html( $child->name );

								if ( ! empty( $child->children ) ) {

									echo '<div class="expandable-content">';

									foreach ( $child->children as $child ) {

										$item_data  = ' data-ml-item-type="' . esc_attr( $child->type ) . '"';
										$item_data .= ' data-ml-item-object="' . esc_attr( $child->object ) . '"';
										$item_data .= ' data-ml-object-id="' . esc_attr( $child->object_id ) . '"';

										$onclick = 'nativeFunctions.handlePost( ' . $child->object_id . ' )';

										if ( $child->type === 'post_type' && $child->opening_method !== 'native' ) {
											$onclick = "nativeFunctions.handleLink( '" . esc_url( $child->url ) . "', '" . esc_attr( $child->name ) . "', '" . esc_attr( $child->opening_method ) . "' )";
										}

										if ( $child->type === 'taxonomy' ) {
											$onclick = "nativeFunctions.handleLink( '" . esc_url( trailingslashit( get_bloginfo( 'url' ) ) . 'ml-api/v2/' . $article_list . '?taxonomy=' . $child->object . '&term_id=' . $child->object_id ) . "', '" . esc_attr( $child->name ) . "', '" . esc_attr( $child->opening_method ) . "' )";
										}

										if ( $child->type === 'custom' ) {
											if ( $child->url !== '#' ) {
												$onclick = "nativeFunctions.handleLink( '" . esc_url( $child->url ) . "', '" . esc_attr( $child->title ) . "', '" . esc_attr( $child->opening_method ) . "' )";
											} else {
												$onclick = '';
											}
										}

										?>

										<ons-list-item data-click="<?php echo $onclick; ?>"
											tappable <?php echo $item_data; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>>
											<?php echo esc_html( $child->name ); ?>
										</ons-list-item>

										<?php

									}

									echo '</div>';
								}
								?>

							</ons-list-item>

							<?php

						}

						echo '</div>';
						?>


					<?php } ?>
				</ons-list-item>

				<?php
			}

			do_action( 'mobiloud_below_sections' );

			?>
		</ons-list>

		<?php
	} else {
		echo 'No menu selected for Sections';
	}
	?>

</ons-page>

<?php wp_footer(); ?>

<script data-cfasync="false">
	var ml_sections_loaded = false;
	document.addEventListener("DOMContentLoaded", function(event) {
		if ( ! ml_sections_loaded ) {
			ml_sections_loaded = true;
			document.querySelectorAll( 'ons-list#sections-placeholder' ).forEach( e => e.parentNode.removeChild( e ) );

			document.querySelectorAll( 'ons-list-item' ).forEach( function( item ) {
				item.addEventListener( 'click', function( e )  {
					e.stopPropagation();
					if ( e.target.classList.contains( 'list-item__right' ) || e.target.parentNode.classList.contains( 'list-item__right' ) ) {
						// clicked on dropdown arrow, do nothing
						return false;
					} else {
						// run the data-click expression
						eval( item.getAttribute( 'data-click' ) );
					}
				} );
			});
		}
	} );
</script>

<?php
// embed any custom JS using this action.
do_action( 'mobiloud_custom_sections_scripts' );
