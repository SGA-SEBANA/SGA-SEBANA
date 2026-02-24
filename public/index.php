<?php

// Front Controller
// public/index.php

// Define base 
define('BASE_PATH', dirname(__DIR__));




// DEBUGGING: Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set default timezone to America/Costa_Rica
date_default_timezone_set('America/Costa_Rica');

// Start session (required for authentication)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load .env file
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Autoloading
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
} else {
    // Manual fallback autoloader
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = BASE_PATH . '/app/';
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
}

// Load Configuration
if (file_exists(BASE_PATH . '/app/config/config.php')) {
    $config = require BASE_PATH . '/app/config/config.php';
} else {
    die("CRITICAL: Config file not found at " . BASE_PATH . '/app/config/config.php');
}

// Initialize Router
use App\Core\Router;

try {
    $router = new Router();
} catch (\Throwable $e) {
    die("CRITICAL: Router initialization failed: " . $e->getMessage());
}

// Load Routes
$modulesDir = BASE_PATH . '/app/modules';

if (is_dir($modulesDir)) {
    $modules = scandir($modulesDir);
    foreach ($modules as $module) {
        if ($module === '.' || $module === '..')
            continue;
        $routesFile = $modulesDir . '/' . $module . '/routes.php';
        if (file_exists($routesFile)) {
            require $routesFile;
        }
    }
}

// Dispatch
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Fix for subdirectories
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
// Normalize slashes
$scriptName = str_replace('\\', '/', $scriptName);

if (strpos($url, $scriptName) === 0) {
    $url = substr($url, strlen($scriptName));
}
if ($url === '') {
    $url = '/';
}

try {
    $router->dispatch($url);
} catch (\Throwable $e) {
    echo "CRITICAL: Dispatch error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}