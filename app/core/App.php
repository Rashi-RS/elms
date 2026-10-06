<?php
declare(strict_types=1);

namespace App\core;

class App
{
    private Router $router;
    private Request $request;
    private Response $response;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->request = new Request();
        $this->response = new Response();
    }

    public function run(): void
    {
        try {
            $this->router->dispatch($this->request, $this->response);
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    private function handleException(\Throwable $e): void
    {
        // Log the exception details securely
        $logDir = STORAGE_PATH . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logMessage = sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        file_put_contents($logDir . '/app.log', $logMessage, FILE_APPEND);

        $this->response->setStatusCode(500);

        if ($this->request->isAjax()) {
            $this->response->json([
                'success' => false,
                'message' => APP_DEBUG ? $e->getMessage() : 'An internal server error occurred.',
                'error' => APP_DEBUG ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ] : null,
            ], 500);
            return;
        }

        $errorView = APP_PATH . '/views/errors/500.php';
        if (file_exists($errorView)) {
            $exception = $e;
            require $errorView;
        } else {
            echo "<h1>500 - Server Error</h1>";
            if (APP_DEBUG) {
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            }
        }
        exit;
    }
}
