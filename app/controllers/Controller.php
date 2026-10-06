<?php
declare(strict_types=1);

namespace App\controllers;

use App\core\Request;
use App\core\Response;
use App\helpers\Csrf;
use App\helpers\Session;
use RuntimeException;

abstract class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function validateCsrf(): void
    {
        $token = $this->request->input('_csrf_token') 
            ?? $_SERVER['HTTP_X_CSRF_TOKEN'] 
            ?? null;

        if (!Csrf::validate($token)) {
            if ($this->request->isAjax()) {
                $this->response->json([
                    'success' => false,
                    'message' => 'CSRF token mismatch. Please refresh and try again.',
                ], 419);
            }

            Session::flash('error', 'Security token expired or invalid. Please try again.');
            $this->response->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    protected function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        // Extract variables for view scope
        extract($data, EXTR_SKIP);

        $viewFile = APP_PATH . '/views/' . ltrim($view, '/') . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: {$viewFile}");
        }

        // Buffer view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render within master layout
        $layoutFile = APP_PATH . '/views/' . ltrim($layout, '/') . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
        exit;
    }

    protected function renderPartial(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = APP_PATH . '/views/' . ltrim($view, '/') . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("Partial view file not found: {$viewFile}");
        }
        require $viewFile;
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        $this->response->json($data, $statusCode);
    }

    protected function redirect(string $url, ?string $flashType = null, ?string $flashMessage = null): void
    {
        if ($flashType && $flashMessage) {
            Session::flash($flashType, $flashMessage);
        }
        $this->response->redirect($url);
    }
}
