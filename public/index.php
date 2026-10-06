<?php
declare(strict_types=1);

/**
 * ELMS - Front Controller
 * Single Entry Point for All Requests
 */

// Load core configuration, constants, session, and autoloader
require_once dirname(__DIR__) . '/config/config.php';

use App\core\App;
use App\core\Router;

// Initialize the route registry
$router = new Router();

// Load application routes
require_once APP_ROOT . '/routes/web.php';

// Bootstrap the application kernel and dispatch request
$app = new App($router);
$app->run();
