<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= BASE_URL ?>">
    <title><?= escape($title ?? 'Dashboard') ?> - SMMP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body class="<?= $_SESSION['theme'] ?? 'dark' ?>">
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar collapsed">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg class="anim-envelope" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
            </div>
            <span class="sidebar-brand-text brand-text">SMMP</span>
            <button id="sidebarToggle" class="sidebar-toggle-brand" title="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="sidebar-inner">
            <nav>
                <div class="sidebar-section sidebar-text">Main</div>
                <a href="dashboard" class="nav-link-smm <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="email_logs" class="nav-link-smm <?= $active_menu === 'email_logs' ? 'active' : '' ?>">
                    <i class="fas fa-envelope-open-text"></i> <span class="sidebar-text">Email Activity</span>
                </a>
                <a href="suppression" class="nav-link-smm <?= $active_menu === 'suppression' ? 'active' : '' ?>">
                    <i class="fas fa-ban"></i> <span class="sidebar-text">Suppression Logs</span>
                </a>

                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <div class="sidebar-section sidebar-text">Management</div>
                <a href="users" class="nav-link-smm <?= $active_menu === 'users' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> <span class="sidebar-text">Users</span>
                </a>
                <a href="smtp_accounts" class="nav-link-smm <?= $active_menu === 'smtp_accounts' ? 'active' : '' ?>">
                    <i class="fas fa-server"></i> <span class="sidebar-text">SMTP Accounts</span>
                </a>
                <a href="departments" class="nav-link-smm <?= $active_menu === 'departments' ? 'active' : '' ?>">
                    <i class="fas fa-building"></i> <span class="sidebar-text">Departments</span>
                </a>
                <a href="security_keys" class="nav-link-smm <?= $active_menu === 'security_keys' ? 'active' : '' ?>">
                    <i class="fas fa-key"></i> <span class="sidebar-text">Security Keys</span>
                </a>
                <a href="settings/suppression_api" class="nav-link-smm <?= $active_menu === 'settings' ? 'active' : '' ?>">
                    <i class="fas fa-cloud-download-alt"></i> <span class="sidebar-text">Suppression API</span>
                </a>
                <?php endif; ?>

                <div class="sidebar-section sidebar-text">Insights</div>
                <a href="analytics" class="nav-link-smm <?= $active_menu === 'analytics' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> <span class="sidebar-text">Analytics</span>
                </a>
                <a href="audit" class="nav-link-smm <?= $active_menu === 'audit' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> <span class="sidebar-text">Audit Logs</span>
                </a>
                <a href="help" class="nav-link-smm <?= $active_menu === 'help' ? 'active' : '' ?>">
                    <i class="fas fa-question-circle"></i> <span class="sidebar-text">Documentation</span>
                </a>

                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <div class="sidebar-section sidebar-text mt-3">System</div>
                <a href="self_update" class="nav-link-smm <?= $active_menu === 'self_update' ? 'active' : '' ?>">
                    <i class="fas fa-sync-alt"></i> <span class="sidebar-text">Self Update</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="sidebar-footer">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="users/edit?id=<?= $_SESSION['user_id'] ?? 0 ?>" class="nav-link-smm">
                <i class="fas fa-cog"></i> <span class="sidebar-text">Settings</span>
            </a>
            <?php endif; ?>
            <a href="auth/logout" class="nav-link-smm">
                <i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Sign Out</span>
            </a>
            <div class="mt-2 px-2 sidebar-text version-text" style="font-size:0.65rem;color:var(--text-muted);">
                v<?= $app_version ?? '1.0.0' ?>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="top-bar">
            <div class="d-flex align-items-center gap-2">
                <button id="mobileSidebarToggle" class="sidebar-toggle d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="d-none d-sm-inline" style="font-size:0.82rem;color:var(--text-secondary);">
                    <?= escape($title ?? 'Dashboard') ?>
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <!-- Signal bars (hide on small screens) -->
                <div class="anim-signal d-none d-md-flex">
                    <div class="anim-signal-bar"></div>
                    <div class="anim-signal-bar"></div>
                    <div class="anim-signal-bar"></div>
                    <div class="anim-signal-bar"></div>
                </div>

                <!-- Theme toggle -->
                <button id="themeToggle" class="theme-toggle" title="Toggle theme">
                    <i class="fas fa-<?= ($_SESSION['theme'] ?? 'dark') === 'dark' ? 'sun' : 'moon' ?>"></i>
                </button>

                <span class="header-clock-wrap" style="font-size:0.8rem;color:var(--text-muted);">
                    <i class="far fa-clock me-1"></i> <span id="headerClock"></span>
                </span>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown" style="color:var(--text-primary);">
                        <div style="width:28px;height:28px;border-radius:6px;background:var(--gradient-accent);display:flex;align-items:center;justify-content:center;font-size:0.7rem;color:#fff;font-weight:600;">
                            <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="d-none d-sm-inline user-name-text" style="font-size:0.82rem;"><?= escape($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:12px;padding:8px;min-width:160px;box-shadow:var(--shadow-lg);">
                        <li><a class="dropdown-item" href="users/edit?id=<?= $_SESSION['user_id'] ?? 0 ?>" style="border-radius:8px;padding:8px 12px;color:var(--text-secondary);"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider" style="border-color:var(--border-color);"></li>
                        <li><a class="dropdown-item" href="auth/logout" style="border-radius:8px;padding:8px 12px;color:var(--text-secondary);"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-content">
            <?php if (flashHas('success')): ?>
                <div class="alert-smm alert-smm-success"><i class="fas fa-check-circle"></i> <?= escape(flash('success')) ?><button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size:0.7rem;filter:invert(0.5);"></button></div>
            <?php endif; ?>
            <?php if (flashHas('error')): ?>
                <div class="alert-smm alert-smm-danger"><i class="fas fa-exclamation-circle"></i> <?= escape(flash('error')) ?><button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size:0.7rem;filter:invert(0.5);"></button></div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </div>

        <footer class="app-footer">
            <div class="d-flex align-items-center justify-content-center gap-1 gap-sm-2 flex-wrap">
                <span class="live-indicator footer-live-text"><span class="live-dot"></span> System Live</span>
                <span class="mx-1 footer-divider" style="color:var(--border-color);">|</span>
                SMTP Management Platform &copy; <?= date('Y') ?>
                <span class="mx-1 footer-divider" style="color:var(--border-color);">|</span>
                <span class="anim-pulse-slow">SMMP v<?= $app_version ?? '1.0.0' ?></span>
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/script.js"></script>
    <script>
    // Live clock
    function updateClock() {
        var now = new Date();
        document.getElementById('headerClock').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
    }
    updateClock();
    setInterval(updateClock, 1000);
    </script>
</body>
</html>
