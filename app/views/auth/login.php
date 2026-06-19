<?php if ($error): ?>
    <div class="alert-smm alert-smm-danger"><i class="fas fa-exclamation-circle"></i> <?= escape($error) ?></div>
<?php endif; ?>

<form method="POST" action="auth/login">
    <div class="input-group-smm">
        <i class="fas fa-user input-icon"></i>
        <input type="text" name="username" class="input-smm" placeholder="Username or Email">
    </div>
    <div class="input-group-smm">
        <i class="fas fa-lock input-icon"></i>
        <input type="password" name="password" class="input-smm" placeholder="Password">
        <button type="button" class="password-toggle" tabindex="-1"><i class="fas fa-eye"></i></button>
    </div>
    <button type="submit" class="btn-auth mb-3">
        <span class="spinner"></span>
        <span class="btn-text"><i class="fas fa-arrow-right-to-bracket me-2"></i> Sign In</span>
    </button>
</form>

<div style="position:relative;text-align:center;margin-bottom:16px;">
    <hr style="border-color:#dce3ef;">
    <span style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:0 12px;font-size:0.75rem;color:#8896b2;">OR</span>
</div>

<a href="auth/microsoft" class="btn-auth" style="background:#fff;color:#0f1a2e;border:1px solid #dce3ef;text-decoration:none;">
    <i class="fab fa-microsoft" style="font-size:1.1rem;"></i>
    <span class="btn-text">Sign in with Microsoft</span>
</a>