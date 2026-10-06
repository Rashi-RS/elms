<?php
declare(strict_types=1);

/**
 * ELMS - Global Application Configuration & Bootstrap
 */

// Define filesystem root constants
define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/app');
define('STORAGE_PATH', APP_ROOT . '/storage');
define('UPLOADS_PATH', APP_ROOT . '/public/uploads');

// Load environment variables from .env if present
(function () {
    $envFile = APP_ROOT . '/.env';
    if (!file_exists($envFile)) {
        $envFile = APP_ROOT . '/.env.example';
    }

    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $val] = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                // Strip surrounding quotes
                if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                    (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                    $val = substr($val, 1, -1);
                }
                $_ENV[$key] = $val;
                putenv("{$key}={$val}");
            }
        }
    }
})();

// Application configuration defaults
define('APP_NAME', $_ENV['APP_NAME'] ?? 'ELMS');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('APP_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'));

// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Kolkata');

// Error reporting configuration
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/app.log');
}

// Secure Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);
    $secure = filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN);

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

// PSR-4 Standard Class Autoloader for `App\` namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
