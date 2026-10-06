<div class="mb-3">
    <h5 class="fw-bold mb-1">Reset Your Password</h5>
    <p class="text-muted small">Enter your verified email address and we will generate a recovery authorization token.</p>
</div>

<form action="<?= \App\helpers\ViewHelper::url('forgot-password') ?>" method="POST">
    <?= \App\helpers\ViewHelper::csrf() ?>

    <div class="mb-4">
        <label for="email" class="form-label fw-semibold small text-secondary">Registered Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="name@company.com" required autofocus>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-sm mb-3">
        <i class="bi bi-send-fill me-1"></i> Send Recovery Token
    </button>

    <div class="text-center">
        <a href="<?= \App\helpers\ViewHelper::url('login') ?>" class="text-decoration-none small text-secondary">
            <i class="bi bi-arrow-left me-1"></i> Return to Sign In
        </a>
    </div>
</form>
