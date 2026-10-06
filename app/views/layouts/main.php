<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\helpers\ViewHelper::e($title ?? 'ELMS — Employee & Leave Management System') ?></title>
    <?= \App\helpers\ViewHelper::csrfMeta() ?>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Enterprise Stylesheet -->
    <link rel="stylesheet" href="<?= \App\helpers\ViewHelper::asset('css/app.css') ?>">
</head>
<body class="d-flex">

    <!-- Sidebar Navigation -->
    <?php if (\App\helpers\Session::isAdmin()): ?>
        <?php require APP_PATH . '/views/partials/admin_sidebar.php'; ?>
    <?php else: ?>
        <?php require APP_PATH . '/views/partials/employee_sidebar.php'; ?>
    <?php endif; ?>

    <!-- Main Content Wrapper -->
    <div id="main-wrapper">
        <!-- Topbar -->
        <?php require APP_PATH . '/views/partials/topbar.php'; ?>

        <!-- Main Body -->
        <main class="flex-grow-1 p-3 p-md-4">
            <!-- Flash Message Alerts -->
            <?php require APP_PATH . '/views/partials/alerts.php'; ?>

            <!-- Rendered View Content -->
            <?= $content ?? '' ?>
        </main>

        <!-- Footer -->
        <?php require APP_PATH . '/views/partials/footer.php'; ?>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js 4.4 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <!-- Custom Application JS -->
    <script src="<?= \App\helpers\ViewHelper::asset('js/app.js') ?>"></script>
    <script src="<?= \App\helpers\ViewHelper::asset('js/charts.js') ?>"></script>
</body>
</html>
