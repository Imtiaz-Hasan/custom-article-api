<?php
/*
Plugin Name: Custom Article API
Description: Exposes a custom REST API for managing articles from external applications (e.g., Laravel).
Version: 1.0.0
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Autoload classes (simple PSR-4 style for this plugin)
spl_autoload_register(function ($class) {
    $prefix = 'CustomArticleApi\\';
    $base_dir = __DIR__ . '/includes/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
add_action('plugins_loaded', function() {
    \CustomArticleApi\Plugin::get_instance();
}); 