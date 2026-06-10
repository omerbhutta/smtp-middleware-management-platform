<div class="auth-brand">
    <div class="auth-brand-icon"><i class="fas fa-shield-halved"></i></div>
    <h2>Two-Factor Auth</h2>
    <p>Enter the OTP sent to your email</p>
</div>

<?php if ($error): ?>
    <div class="alert-smm alert-smm-danger"><i class="fas fa-exclamation-circle"></i> <?= escape($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert-smm alert-smm-success"><i class="fas fa-check-circle"></i> <?= escape($success) ?></div>
<?php endif; ?>

<form method="POST" action="auth/mfa">
    <div class="mb-4">
        <label class="form-label-smm">OTP Code</label>
        <div class="input-group-smm">
            <i class="fas fa-key"></i>
            <input type="text" name="code" class="input-smm" placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required
                   style="font-size: 1.5rem; letter-spacing: 8px; text-align: center;">
        </div>
    </div>
    <button type="submit" class="btn-auth">
        <i class="fas fa-check-circle me-2"></i> Verify & Sign In
    </button>
    <div class="text-center mt-3">
        <a href="auth/login" style="color: #6b7280; font-size: 0.82rem; text-decoration: none;">
            <i class="fas fa-arrow-left me-1"></i> Back to login
        </a>
    </div>
</form>

<div class="auth-footer">
    SMTP Management Platform
</div>
