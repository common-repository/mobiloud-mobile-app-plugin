<?php
	$section_one_data = array(
		array(
			'header'   => 'DOCUMENTATION',
			'title'    => 'Getting started',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/question.svg',
			'pill_url' => 'https://www.mobiloud.com/help/article-categories/getting-started',
		),
		array(
			'header'   => 'DOCUMENTATION',
			'title'    => 'Configure the menus',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/menu-book.svg',
			'pill_url' => 'https://www.mobiloud.com/help/article-categories/configuring-menus',
		),
		array(
			'header'   => 'DOCUMENTATION',
			'title'    => 'Sending notifications',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/bell.svg',
			'pill_url' => 'https://www.mobiloud.com/help/article-categories/push-notifications-news-commerce-configuration',
		),
		array(
			'header'   => 'DOCUMENTATION',
			'title'    => 'Advertising',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/grid.svg',
			'pill_url' => 'https://www.mobiloud.com/help/article-categories/advertising',
		),
		array(
			'header'   => 'DOCUMENTATION',
			'title'    => 'Customizations',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/palette.svg',
			'pill_url' => 'https://www.mobiloud.com/help/article-categories/customizations',
		),
		array(
			'header'   => 'DOCUMENTATION',
			'title'    => 'Subscriptions',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/dollar.svg',
			'pill_url' => 'https://www.mobiloud.com/help/article-categories/subscriptions',
		),
	);

	$section_two_data = array(
		array(
			'header'   => 'LEARN MORE',
			'title'    => 'Knowledge Base',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/menu-book.svg',
			'pill_url' => 'https://www.mobiloud.com/help',
		),
		array(
			'header'   => 'GET SUPPORT',
			'title'    => 'Contact Us',
			'icon_url' => MOBILOUD_PLUGIN_URL . 'assets/icons/steer.svg',
			'pill_url' => 'https://www.mobiloud.com/contact',
		),
	);
?>

<div class="mlsw__config-root">
	<div class="mlsw__config-title">
		Configure your app
	</div>
	<div class="mlsw__config-desc">
		To make things easier for you we have configured your app using the most popular categories from your website. You can now go ahead and adjust your app’s configuration using the plugin's settings. To help you with getting started we have created some handy guides, check them out below:
	</div>
	<hr class="mlsw__config-separator">
	<div class="mlsw__config-section-title">
		Learn how to use it
	</div>

	<div class="mlsw__config-section-grid mlsw__config-section-grid--section-one">
		<?php foreach ( $section_one_data as $pill ) : ?>
			<div class="mlsw__config-section-pill">
				<a target="_blank" href="<?php echo esc_url( $pill['pill_url'] ); ?>">
					<img class="mlsw__config-pill-icon" src="<?php echo esc_url( $pill['icon_url'] ); ?>" />
					<div class="mlsw__config-pill-text-wrapper">
						<div class="mlsw__config-pill-header">
							<?php echo esc_html( $pill['header'] ); ?>
						</div>
						<div class="mlsw__config-pill-title">
							<?php echo esc_html( $pill['title'] ); ?>
						</div>
					</div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>

	<hr class="mlsw__config-separator">
	<div class="mlsw__config-section-title">
		Get help
	</div>
	<div class="mlsw__config-section-grid mlsw__config-section-grid--section-two">
		<?php foreach ( $section_two_data as $pill ) : ?>
			<div class="mlsw__config-section-pill">
				<a target="_blank" href="<?php echo esc_url( $pill['pill_url'] ); ?>">
					<img class="mlsw__config-pill-icon" src="<?php echo esc_url( $pill['icon_url'] ); ?>" />
					<div class="mlsw__config-pill-text-wrapper">
						<div class="mlsw__config-pill-header">
							<?php echo esc_html( $pill['header'] ); ?>
						</div>
						<div class="mlsw__config-pill-title">
							<?php echo esc_html( $pill['title'] ); ?>
						</div>
					</div>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="mlsw__button-controls">
		<a href="<?php echo admin_url( 'admin.php?page=mobiloud&step=design' ); ?>" type="submit" name="back" class="mlsw__button mlsw__button--gray"><?php esc_html_e( 'Back' ); ?></a>
		<a href="<?php echo admin_url( 'admin.php?page=mobiloud&step=welcome-close' ); ?>" type="submit" name="finish" class="mlsw__button mlsw__button--blue"><?php esc_html_e( 'Finish' ); ?></a>
	</div>
</div>
