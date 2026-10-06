<?php
$flashes = \App\helpers\Session::allFlashes();
?>

<?php if (!empty($flashes)): ?>
    <div class="alerts-wrapper mb-3">
        <?php foreach ($flashes as $type => $messages): ?>
            <?php
            $alertClass = match ($type) {
                'success' => 'alert-success',
                'error' => 'alert-danger',
                'warning' => 'alert-warning',
                default => 'alert-info',
            };
            $icon = match ($type) {
                'success' => 'bi-check-circle-fill',
                'error' => 'bi-exclamation-octagon-fill',
                'warning' => 'bi-exclamation-triangle-fill',
                default => 'bi-info-circle-fill',
            };
            ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert <?= $alertClass ?> alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0" role="alert">
                    <i class="bi <?= $icon ?> fs-5"></i>
                    <div><?= $msg ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
