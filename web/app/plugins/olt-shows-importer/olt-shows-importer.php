<?php
/**
 * Plugin Name: OLT Shows Importer
 * Description: Imports shows from the Official London Theatre API
 * Version: 1.0.0
 * Author: Farlo
 * Requires at least: 6.0
 * Requires PHP: 8.2
 */

declare(strict_types=1);

namespace OLT\ShowsImporter;

if (!defined('ABSPATH')) {
    exit;
}

define('OLT_IMPORTER_VERSION', '1.0.0');
define('OLT_IMPORTER_PATH', plugin_dir_path(__FILE__));

// Autoload classes
spl_autoload_register(function (string $class): void {
    $prefix = 'OLT\\ShowsImporter\\';
    $base_dir = OLT_IMPORTER_PATH . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);

    // Convert class name to filename: API_Client -> class-api-client.php
    $file_name = 'class-' . strtolower(str_replace('_', '-', $relative_class)) . '.php';
    $file = $base_dir . $file_name;

    if (file_exists($file)) {
        require $file;
    }
});

// Register WP-CLI command
if (defined('WP_CLI') && WP_CLI) {
    \WP_CLI::add_command('import-shows', CLI_Command::class);
}
