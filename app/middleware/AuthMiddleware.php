<?php
declare(strict_types=1);

namespace App\middleware;

use App\core\Request;
use App\core\Response;
use App\helpers\Session;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, Response $response): void
    {
        if (!Session::isLoggedIn()) {
            if ($request->isAjax()) {
                $response->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in.',
                ], 401);
            }

            Session::flash('error', 'Please log in to access this page.');
            $response->redirect('/login');
        }
    }
}
