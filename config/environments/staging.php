<?php
/**
 * Configuration overrides for WP_ENV === 'staging'
 */

use Roots\WPConfig\Config;

Config::define('ENFORCE_GZIP', true);
Config::define('COMPRESS_SCRIPTS', true);
Config::define('COMPRESS_CSS', true);
Config::define('CONCATENATE_SCRIPTS', true);
Config::define('DISALLOW_INDEXING', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', false);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('DISALLOW_FILE_EDIT', true);
Config::define('DISALLOW_FILE_MODS', false);
Config::define('FS_METHOD', 'direct');
Config::define('WP_ENVIRONMENT_TYPE', 'staging');

Config::define('WP_CACHE_KEY_SALT', 'MpQ9mH6tXw7zN2Bs5oGdRjK1V3PfEy4YQn5WArx8u4sff');
