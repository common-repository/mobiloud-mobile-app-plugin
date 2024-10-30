<?php

/** @var string $canvas_site_url */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
<style>
    body {
        overflow-x: hidden;
    }
    #ml-header .canvas-header-inner {
        padding: 30px 0;
        max-width: 950px;
        width: calc(100% - 30px);
        margin: 0 auto;
    }
    #wpcontent {
        padding-left: 0;
    }
    body, #wpcontent, #wpfooter {
        background: #f9f9f9;
    }
    #wrap {
        margin: 0;
    }
    #ml-header {
        padding: 0px 0 0px;
    }
</style>

<div class="news-app-builder-welcome">
    <h2><?php esc_html_e('Get a demo of your mobile app', 'mobiloud'); ?></h2>
    <p><?php esc_html_e("MobiLoud helps you turn your WordPress website into native mobile apps that work with everything on your website and require no effort to maintain.", 'mobiloud'); ?></p>
    <p><?php esc_html_e("Book a free demo to learn more about our process and get a working preview of your mobile app prepared by our team.", 'mobiloud'); ?></p>
    <form action="<?php echo admin_url('admin.php'); ?>?page=mobiloud&news-app-builder=1" method="post">
        <button class="app-builder-btn" type="submit">Book a Free Demo</button>
        <div class="button-small-text">
            <span><?php esc_html_e("Free to try. No credit card required.", 'canvas'); ?></span>
        </div>
    </form>
</div>