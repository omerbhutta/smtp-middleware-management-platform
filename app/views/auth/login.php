<div class="auth-brand">
    <div class="auth-brand-icon"><i class="fas fa-envelope-circle-check"></i></div>
    <h2>SMMP</h2>
    <p>SMTP Middleware Management Platform</p>
</div>

<?php if ($error): ?>
    <div class="alert-smm alert-smm-danger"><i class="fas fa-exclamation-circle"></i> <?= escape($error) ?></div>
<?php endif; ?>

<form method="POST" action="auth/login">
    <div class="mb-4">
        <label class="form-label-smm">Username or Email</label>
        <div class="input-group-smm">
            <i class="fas fa-user"></i>
            <input type="text" name="username" class="input-smm" placeholder="Enter your username or email" required>
        </div>
    </div>
    <div class="mb-4">
        <label class="form-label-smm">Password</label>
        <div class="input-group-smm">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" class="input-smm" placeholder="Enter your password" required>
        </div>
    </div>
    <button type="submit" class="btn-auth">
        <i class="fas fa-arrow-right-to-bracket me-2"></i> Sign In
    </button>
</form>

<div class="auth-footer">
    SMTP Management Platform
</div>
