<?php
/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;
use function Env\env;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', env('WP_DEBUG_LOG') ?? true);
Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
Config::define('SCRIPT_DEBUG', true);
Config::define('COMPRESS_SCRIPTS', true);
Config::define('COMPRESS_CSS', true);
Config::define('CONCATENATE_SCRIPTS', true);
Config::define('WP_LOCAL_DEV', true);
Config::define('DISALLOW_INDEXING', true);
Config::define('WP_ENVIRONMENT_TYPE', 'development');
Config::define("WP_CACHE_KEY_SALT", "8ZLnK1KR3gByiQp45QtNw5LS5tTafOB9haFE5od32d3");

ini_set('display_errors', '1');

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);
Config::define('DISALLOW_FILE_EDIT', false);
