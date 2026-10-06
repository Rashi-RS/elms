<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= \App\helpers\ViewHelper::asset('css/app.css') ?>">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="text-center" style="max-width: 480px;">
        <div class="display-1 text-primary fw-bold">404</div>
        <h1 class="fw-bold mb-2">Page Not Found</h1>
        <p class="text-muted mb-4">The URL route or resource you requested could not be located on the server.</p>
        <a href="<?= \App\helpers\ViewHelper::url('/') ?>" class="btn btn-primary px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to Safety
        </a>
    </div>
</body>
</html>
