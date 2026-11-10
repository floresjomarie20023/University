<form method="POST" action="/login" novalidate>
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   id="email" name="email" value="<?= htmlspecialchars((string)$old('email')) ?>"
                   placeholder="you@university.edu" autocomplete="email" required autofocus>
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['email'][0]) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Enter your password" autocomplete="current-password" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign in
    </button>
</form>

<div class="mt-4 p-3 bg-light rounded-3 small text-muted">
    <div class="fw-semibold text-dark mb-1"><i class="bi bi-info-circle me-1"></i>Demo accounts</div>
    <div><strong>Admin:</strong> admin@university.edu / password</div>
    <div><strong>Teacher:</strong> teacher@university.edu / password</div>
</div>
