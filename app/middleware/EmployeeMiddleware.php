<?php
declare(strict_types=1);

namespace App\middleware;

use App\core\Request;
use App\core\Response;
use App\helpers\Session;

class EmployeeMiddleware implements Middleware
{
    public function handle(Request $request, Response $response): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Please log in to continue.');
            $response->redirect('/login');
        }

        if (!Session::isEmployee() && !Session::isAdmin()) {
            if ($request->isAjax()) {
                $response->json([
                    'success' => false,
                    'message' => 'Forbidden: Employee access required.',
                ], 403);
            }

            $response->setStatusCode(403);
            $errorView = APP_PATH . '/views/errors/403.php';
            if (file_exists($errorView)) {
                require $errorView;
            } else {
                echo "<h1>403 - Forbidden</h1>";
            }
            exit;
        }
    }
}
