<?php
declare(strict_types=1);

namespace App\middleware;

use App\core\Request;
use App\core\Response;

interface Middleware
{
    public function handle(Request $request, Response $response): void;
}
