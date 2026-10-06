<form action="<?= \App\helpers\ViewHelper::url('login') ?>" method="POST" class="needs-validation" novalidate>
    <?= \App\helpers\ViewHelper::csrf() ?>

    <!-- Email Field -->
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold small text-secondary">Work Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" value="<?= \App\helpers\ViewHelper::e($email ?? '') ?>" placeholder="name@company.com" required autofocus>
        </div>
    </div>

    <!-- Password Field -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <label for="password" class="form-label fw-semibold small text-secondary mb-0">Password</label>
            <a href="<?= \App\helpers\ViewHelper::url('forgot-password') ?>" class="small text-decoration-none text-primary">Forgot Password?</a>
        </div>
        <div class="input-group mt-1">
            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
            <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" placeholder="••••••••" required>
        </div>
    </div>

    <!-- Remember Me Checkbox -->
    <div class="mb-4 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
        <label class="form-check-label small text-muted" for="remember">Keep me logged in on this browser</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-sm">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Portal
    </button>
</form>

<!-- Demo Credentials Helper Banner for Interview & Portfolio Review -->
<div class="card bg-light border-0 mt-4 p-3 rounded-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="fw-bold small text-dark"><i class="bi bi-info-circle-fill text-primary me-1"></i> Demo Credentials</span>
        <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.65rem;">ONE-CLICK FILL</span>
    </div>

    <div class="d-grid gap-2">
        <button type="button" class="btn btn-sm btn-outline-primary text-start d-flex justify-content-between align-items-center" onclick="autofill('admin@elms.test', 'Admin@123')">
            <span><strong>Admin:</strong> admin@elms.test</span>
            <span class="badge bg-primary">Auto-fill</span>
        </button>

        <button type="button" class="btn btn-sm btn-outline-secondary text-start d-flex justify-content-between align-items-center" onclick="autofill('alex.turner@elms.test', 'Employee@123')">
            <span><strong>Employee:</strong> alex.turner@elms.test</span>
            <span class="badge bg-secondary">Auto-fill</span>
        </button>
    </div>
</div>

<script>
function autofill(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
