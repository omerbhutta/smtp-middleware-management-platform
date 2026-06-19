<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= BASE_URL ?>">
    <title><?= escape($title ?? 'Login') ?> - SMMP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .bg-grid {
            position: fixed; inset: 0; z-index: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .bg-glow {
            position: fixed; z-index: 0; border-radius: 50%; filter: blur(100px); opacity: 0.06;
        }
        .bg-glow-1 { width: 500px; height: 500px; background: #3b82f6; top: -200px; left: -100px; }
        .bg-glow-2 { width: 400px; height: 400px; background: #06b6d4; bottom: -200px; right: -100px; }

        .auth-container {
            position: relative; z-index: 1;
            width: 100%; max-width: 400px; padding: 20px;
        }

        .auth-card {
            background: linear-gradient(145deg, #ffffff, #f0f6ff);
            border: 1px solid #dce3ef;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.02);
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff;
            margin-bottom: 14px;
            box-shadow: 0 4px 12px rgba(59,130,246,0.2);
        }
        .auth-brand h2 {
            color: #0f1a2e;
            font-size: 1.35rem; font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 2px;
        }
        .auth-brand p {
            color: #8896b2;
            font-size: 0.8rem;
        }

        .alert-smm {
            padding: 12px 16px; border-radius: 12px;
            font-size: 0.85rem; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-smm-danger { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.15); color: #dc2626; }

        .input-group-smm {
            position: relative; margin-bottom: 16px;
        }
        .input-group-smm .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #8896b2; font-size: 0.9rem;
            transition: color 0.3s; pointer-events: none; z-index: 2;
        }
        .input-group-smm .input-smm {
            width: 100%; padding: 12px 42px 12px 42px;
            background: #f0f4f9;
            border: 1px solid #dce3ef;
            border-radius: 12px;
            color: #0f1a2e;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .input-group-smm .input-smm:focus {
            outline: none;
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.08);
        }
        .input-group-smm .input-smm::placeholder { color: #8896b2; }
        .input-group-smm.focused .input-icon { color: #3b82f6; }

        .password-toggle {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #8896b2; cursor: pointer;
            font-size: 0.85rem; padding: 4px; z-index: 2;
            transition: color 0.3s;
        }
        .password-toggle:hover { color: #4a5a7a; }

        .form-check-smm {
            display: flex; align-items: center; gap: 8px; cursor: pointer;
            margin-bottom: 22px;
        }
        .form-check-smm input[type="checkbox"] {
            appearance: none; -webkit-appearance: none;
            width: 18px; height: 18px;
            border: 1px solid #dce3ef; border-radius: 5px;
            background: #fff;
            cursor: pointer; position: relative;
            transition: all 0.25s; flex-shrink: 0;
        }
        .form-check-smm input[type="checkbox"]:checked {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-color: transparent;
        }
        .form-check-smm input[type="checkbox"]:checked::after {
            content: '\f00c'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 0.55rem;
        }
        .form-check-smm label { color: #4a5a7a; font-size: 0.82rem; cursor: pointer; }

        .btn-auth {
            width: 100%; padding: 12px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none; border-radius: 12px;
            color: #fff; font-size: 0.9rem; font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-auth:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(59,130,246,0.25);
        }
        .btn-auth:active { transform: translateY(0); }
        .btn-auth:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .btn-auth .spinner {
            display: none;
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        .btn-auth.loading .spinner { display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .auth-footer {
            text-align: center; margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #dce3ef;
            color: #8896b2; font-size: 0.78rem;
        }
        .auth-footer .version-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            background: #f0f4f9;
            border: 1px solid #dce3ef;
            color: #4a5a7a;
            font-size: 0.65rem;
            font-weight: 500;
        }

        .mfa-section { display: none; }
        .mfa-section.active { display: block; }

        @media (max-width: 480px) {
            .auth-card { padding: 28px 20px; }
            .auth-container { padding: 12px; }
        }
    </style>
</head>
<body>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-grid"></div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="fas fa-envelope-circle-check"></i></div>
                <h2>SMMP</h2>
                <p>SMTP Middleware Management Platform</p>
            </div>
            <?= $content ?? '' ?>
            <div class="auth-footer">
                <span class="version-badge">v<?= $app_version ?? '1.0.0' ?></span>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.input-group-smm .input-smm').forEach(function(input) {
        input.addEventListener('focus', function() { this.closest('.input-group-smm').classList.add('focused'); });
        input.addEventListener('blur', function() { if (!this.value) this.closest('.input-group-smm').classList.remove('focused'); });
        if (input.value) input.closest('.input-group-smm').classList.add('focused');
    });

    document.querySelectorAll('.password-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentElement.querySelector('.input-smm');
            input.type = input.type === 'password' ? 'text' : 'password';
            this.innerHTML = input.type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    });

    var loginForm = document.querySelector('form[action*="auth/login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function() {
            var btn = this.querySelector('.btn-auth');
            if (btn) btn.classList.add('loading');
        });
    }
    </script>
</body>
</html>