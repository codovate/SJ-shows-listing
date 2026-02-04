<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" media="(prefers-color-scheme: light)" content="white">
        <meta name="theme-color" media="(prefers-color-scheme: dark)" content="black">

        <?php // Add a pingback url auto-discovery header for singularly identifiable articles. ?>
        <?php if (is_singular() && pings_open()) : ?>
            <link rel="pingback" href="<?php echo esc_url(get_bloginfo('pingback_url')); ?>">
        <?php endif; ?>

        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>

        <div id="page" class="site">
            <a class="skip-link screen-reader-text" href="#site-main">
                <?php esc_html_e('Skip to content', 'farlo'); ?>
            </a>
            <header></header>
            <main id="site-main" class="site-main" role="main">
