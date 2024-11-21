<?php
// Define base paths
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Define URL base for the project
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Locale/');
}

// Define absolute paths for includes
if (!defined('COMPONENTS_PATH')) {
    define('COMPONENTS_PATH', ROOT_PATH . '/components');
}
if (!defined('INCLUDES_PATH')) {
    define('INCLUDES_PATH', ROOT_PATH . '/includes');
}
if (!defined('PAGES_PATH')) {
    define('PAGES_PATH', ROOT_PATH . '/pages');
}
if (!defined('ASSETS_PATH')) {
    define('ASSETS_PATH', ROOT_PATH . '/assets');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

// Define URL paths for assets
if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', BASE_URL . 'assets');
}
if (!defined('IMAGES_URL')) {
    define('IMAGES_URL', ASSETS_URL . '/images');
}
?> 