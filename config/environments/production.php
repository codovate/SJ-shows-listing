<?php
/**
 * Configuration overrides for WP_ENV === 'production'
 */

use Roots\WPConfig\Config;

Config::define('ENFORCE_GZIP', true);
Config::define('COMPRESS_SCRIPTS', true);
Config::define('COMPRESS_CSS', true);
Config::define('CONCATENATE_SCRIPTS', true);
Config::define('DISALLOW_INDEXING', false);
Config::define('WP_DEBUG', false);
Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', false);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('DISALLOW_FILE_EDIT', true);
Config::define('DISALLOW_FILE_MODS', false);
Config::define('FS_METHOD', 'direct');
Config::define('WP_ENVIRONMENT_TYPE', 'production');

Config::define("WP_CACHE_KEY_SALT", "9kufuXlr7rbgFqAxOMTlz74Ib4eZvcMF4vVwBoa4fss");

