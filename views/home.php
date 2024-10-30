
<!-- Panel: Learn how to use it -->
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Learn how to use it', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper mlconf__panel-grid mlconf__panel-grid-3by2">
		<?php
			$learning_tabs = Mobiloud_Admin::$learning_tabs;
			foreach ( $learning_tabs as $tab ) : ?>
				<div class="mlconf__tab">
					<a target="_blank" href="<?php echo esc_url( $tab['pill_url'] ); ?>">
						<img class="mlconf__tab-icon" src="<?php echo esc_url( $tab['icon_url'] ); ?>" />
						<div class="mlconf__tab-text-wrapper">
							<div class="mlconf__tab-header">
								<?php echo esc_html( $tab['header'] ); ?>
							</div>
							<div class="mlconf__tab-title">
								<?php echo esc_html( $tab['title'] ); ?>
							</div>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
	</div>
</div>
<!-- Panel: Learn how to use it // -->

<!-- Panel: Useful links -->
<div class="mlconf__panel mlconf__panel--collapsible">
	<div class="mlconf__panel-title">
		<?php esc_html_e( 'Useful links', 'mobiloud' ); ?>
	</div>

	<div class="mlconf__panel-content-wrapper mlconf__panel-grid mlconf__panel-grid-3by1">
		<?php
			$useful_links = Mobiloud_Admin::$useful_links;
			foreach ( $useful_links as $tab ) : ?>
				<div class="mlconf__tab">
					<a target="_blank" href="<?php echo esc_url( $tab['pill_url'] ); ?>">
						<img class="mlconf__tab-icon" src="<?php echo esc_url( $tab['icon_url'] ); ?>" />
						<div class="mlconf__tab-text-wrapper">
							<div class="mlconf__tab-header">
								<?php echo esc_html( $tab['header'] ); ?>
							</div>
							<div class="mlconf__tab-title">
								<?php echo esc_html( $tab['title'] ); ?>
							</div>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
	</div>
</div>
<!-- Panel: Useful links // -->