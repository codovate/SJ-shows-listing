<?php
// Configuration common to all VersionPress environments, included from `wp-config.php`.
// Learn more at https://docs.versionpress.net/en/getting-started/configuration

if (defined('WP_ENV') && WP_ENV === 'development') {
    add_filter('https_ssl_verify', '__return_false');
}
