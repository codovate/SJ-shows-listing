<?php
/**
 * Farlo theme functions.
 */

declare(strict_types=1);

add_action('wp_enqueue_scripts', function (): void {
    $theme_uri  = get_stylesheet_directory_uri();
    $theme_path = get_stylesheet_directory();

    $css_rel = '/dist/theme.css';
    $js_rel  = '/dist/theme.js';

    if (file_exists($theme_path . $css_rel)) {
        wp_enqueue_style('farlo-theme', $theme_uri . $css_rel, [], filemtime($theme_path . $css_rel));
    } else {
        // Fallback so theme still renders without a build step
        wp_enqueue_style('farlo-theme', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    }

    if (file_exists($theme_path . $js_rel)) {
        wp_enqueue_script('farlo-theme', $theme_uri . $js_rel, [], filemtime($theme_path . $js_rel), true);
    }
});