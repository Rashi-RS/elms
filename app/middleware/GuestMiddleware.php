<?php
declare(strict_types=1);

namespace App\middleware;

use App\core\Request;
use App\core\Response;
use App\helpers\Session;

class GuestMiddleware implements Middleware
{
    public function handle(Request $request, Response $response): void
    {
        if (Session::isLoggedIn()) {
            if (Session::isAdmin()) {
                $response->redirect('/admin/dashboard');
            } else {
                $response->redirect('/employee/dashboard');
            }
        }
    }
}
