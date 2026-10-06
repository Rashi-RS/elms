<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\helpers\ViewHelper::e($title ?? 'Sign In — ELMS') ?></title>
    <?= \App\helpers\ViewHelper::csrfMeta() ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= \App\helpers\ViewHelper::asset('css/app.css') ?>">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light p-3">

    <div class="auth-card card p-4 p-sm-5 bg-white shadow-sm">
        <!-- Brand Header -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 p-3 mb-3 shadow-sm" style="width: 52px; height: 52px;">
                <i class="bi bi-briefcase-fill fs-3"></i>
            </div>
            <h3 class="fw-bold tracking-tight mb-1">ELMS Portal</h3>
            <p class="text-muted small mb-0">Employee & Leave Management System</p>
        </div>

        <!-- Flash Message Alerts -->
        <?php require APP_PATH . '/views/partials/alerts.php'; ?>

        <!-- Auth Content -->
        <?= $content ?? '' ?>

        <!-- Footer Notice -->
        <div class="text-center mt-4 pt-3 border-top">
            <small class="text-muted">Core PHP 8.2 &bull; MVC Architecture &bull; PDO & MySQL</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= \App\helpers\ViewHelper::asset('js/app.js') ?>"></script>
</body>
</html>
