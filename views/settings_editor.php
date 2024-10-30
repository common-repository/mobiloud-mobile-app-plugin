<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Code Editor', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<p>You can use the editor to inject HTML, PHP, CSS and Javascript code in a number of positions within the post and page screens. You can reference the current post id using $post->id.</p>

			<p>Read more in our 
				<a href="https://www.mobiloud.com/help/knowledge-base/using-the-code-editor/<?php echo esc_url( get_option( 'affiliate_link', null ) ); ?>?utm_source=wp-plugin-admin&utm_medium=web&utm_campaign=editor"
					target="_blank">Knowledge Base</a>. Need any help? <a class="contact" href="mailto:support@mobiloud.com">Contact our
					support team</a>.
			</p>

			<p><em>Note: this is for developers and advanced users only.</em></p>

			<div class="ml-editor-controls">
				<select id="ml_admin_post_customization_select" name="ml_admin_post_customization_select">
					<option value="">
						Select a customization...
					</option>
					<?php
					foreach ( Mobiloud_Admin::$editor_sections as $editor_key => $editor_name ) :
						$field_type = 'php';
						if ( false !== strpos( $editor_key, 'js' ) ) {
							$field_type = 'javascript';
						} elseif ( false !== strpos( $editor_key, 'css' ) ) {
							$field_type = 'css';
						} elseif ( false !== strpos( $editor_key, 'html' ) ) {
							$field_type = 'htmlmixed';
						}
						?>
						<option value="<?php echo esc_attr( $editor_key ); ?>" data-type="<?php echo esc_attr( $field_type ); ?>">
							<?php echo esc_html( $editor_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<a href="#" class='button-primary ml-save-editor-btn'>Save</a>
				<?php wp_nonce_field( 'save_editor', 'ml_nonce_editor' ); ?>
			</div>
			<textarea class='ml-editor-area ml-show'></textarea>
			<?php foreach ( Mobiloud_Admin::$editor_sections as $editor_key => $editor_name ) : ?>
				<textarea class='ml-editor-area'
					name='<?php echo esc_attr( $editor_key ); ?>'><?php echo stripslashes( htmlspecialchars( Mobiloud::get_option( $editor_key, '' ) ) ); ?></textarea>
			<?php endforeach; ?>
			<?php
			require_once __DIR__ . '/block-preview.php';
			?>
		</div>
	</div>
</div>

<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Custom CSS for embedded pages', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<div class="ml-editor-controls">
				<div class='as-select'>Inject CSS in the website's theme when viewed in the app.</div>
				<a href="#" class='button-primary ml-save-editor-embed-btn'>Save</a>
				<?php wp_nonce_field( 'save_editor_embed', 'ml_nonce_editor_embed' ); ?>
			</div>
			<textarea class='ml-editor-area-embed ml-settings-embed ml-editor-area-css'
				name='ml_embedded_page_css'><?php echo stripslashes( htmlspecialchars( Mobiloud::get_option( 'ml_embedded_page_css', '' ) ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?></textarea>
			<p><em>Use this to add CSS rules to hide elements or change the display of your website when embedded in the app. This will
				affect pages loaded from internal links within articles or pages added from the Link section in the Menu configuration page.</em></p>
		</div>
	</div>
</div>

<?php do_action( 'mobiloud_admin_editors_before_support' ); ?>
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Need help from a pro?', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper">
		<div class='ml-col-row'>
			<p>The Mobiloud developer team can help you integrate custom fields, add video/audio embeds and
				much more to your app, for more information, contact <a href='mailto:support@mobiloud.com'>support@mobiloud.com</a>.
			</p>
		</div>
	</div>
</div>
