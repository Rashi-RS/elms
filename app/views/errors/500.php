<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Internal Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= \App\helpers\ViewHelper::asset('css/app.css') ?>">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="text-center" style="max-width: 600px;">
        <div class="display-1 text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <h1 class="fw-bold mb-2">500 — Server Error</h1>
        <p class="text-muted mb-4">An unexpected condition prevented the application from fulfilling your request. This event has been securely recorded to the application logs.</p>
        
        <?php if (defined('APP_DEBUG') && APP_DEBUG && isset($exception)): ?>
            <div class="alert alert-danger text-start small overflow-auto font-monospace p-3 text-wrap" style="max-height: 250px;">
                <strong><?= htmlspecialchars($exception->getMessage()) ?></strong><br>
                in <?= htmlspecialchars($exception->getFile()) ?>:<?= $exception->getLine() ?><br><br>
                <pre class="mb-0"><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
            </div>
        <?php endif; ?>

        <a href="<?= \App\helpers\ViewHelper::url('/') ?>" class="btn btn-primary px-4 mt-2">
            <i class="bi bi-arrow-clockwise me-1"></i> Return to Homepage
        </a>
    </div>
</body>
</html>
