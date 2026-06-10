<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3><i class="fas fa-cloud-download-alt" style="color:var(--blue-primary);margin-right:8px;"></i> Suppression Sync API</h3>
            </div>
            <div class="card-smm-body">
                <p style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:20px;">
                    Configure the external API endpoint that provides the list of suppressed emails.
                    A cron job service (e.g. cron-job.org, UptimeRobot) will periodically hit the
                    secure URL below to sync them into the local database.
                </p>

                <form method="POST">
                    <input type="hidden" name="action" value="save">

                    <div class="mb-3">
                        <label class="form-label-smm">API Endpoint URL</label>
                        <input type="url" name="endpoint" class="form-control-smm"
                               value="<?= escape($config['endpoint']) ?>"
                               placeholder="https://ebm.jhealth.us/api/suppression" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-smm">API Key (X-Api-Key header)</label>
                        <div class="input-group" style="display:flex;gap:6px;">
                            <input type="password" name="api_key" id="apiKeyInput" class="form-control-smm"
                                   value="<?= escape($config['api_key']) ?>"
                                   placeholder="Enter API key" required style="flex:1;">
                            <button type="button" class="btn-smm btn-smm-secondary btn-smm-sm" onclick="toggleApiKey()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-smm">HTTP Method</label>
                        <select name="method" class="form-control-smm">
                            <option value="GET" <?= $config['method'] === 'GET' ? 'selected' : '' ?>>GET</option>
                            <option value="POST" <?= $config['method'] === 'POST' ? 'selected' : '' ?>>POST</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-smm btn-smm-primary">
                        <i class="fas fa-save"></i> Save Configuration
                    </button>
                </form>
            </div>
        </div>

        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3><i class="fas fa-sync-alt" style="color:var(--emerald);margin-right:8px;"></i> Manual Sync</h3>
            </div>
            <div class="card-smm-body">
                <p style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:14px;">
                    Test the connection and pull suppressed emails from the configured endpoint immediately.
                </p>
                <form method="POST" onsubmit="return confirm('Sync suppressed emails from the external API?');">
                    <input type="hidden" name="action" value="sync">
                    <button type="submit" class="btn-smm btn-smm-success" <?= empty($config['endpoint']) || empty($config['api_key']) ? 'disabled style="opacity:0.5;"' : '' ?>>
                        <i class="fas fa-play"></i> Run Sync Now
                    </button>
                </form>
            </div>
        </div>

        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3><i class="fas fa-clock" style="color:var(--amber);margin-right:8px;"></i> Cron Job URL</h3>
            </div>
            <div class="card-smm-body">
                <p style="font-size:0.82rem;color:var(--text-secondary);margin-bottom:10px;">
                    Set up a free cron job service (<a href="https://cron-job.org" target="_blank" style="color:var(--blue-primary);">cron-job.org</a>,
                    <a href="https://uptimerobot.com" target="_blank" style="color:var(--blue-primary);">UptimeRobot</a>, etc.)
                    to hit this URL every 5 minutes:
                </p>
                <div class="key-display" style="padding:10px 12px;font-size:0.78rem;word-break:break-all;">
                    <code class="key-value" style="color:var(--text-primary);"><?= escape($cronUrl) ?></code>
                    <button class="btn-smm btn-smm-secondary btn-smm-xs" style="flex-shrink:0;" onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent).then(()=>{this.innerHTML='<i class=\'fas fa-check\'></i>';setTimeout(()=>this.innerHTML='<i class=\'fas fa-copy\'></i>',1500)})">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="mt-3 d-flex align-items-center gap-2">
                    <a href="settings/suppression_api?regenerate_key=1" class="btn-smm btn-smm-secondary btn-smm-sm" onclick="return confirm('Regenerate the cron key? The old URL will stop working.');">
                        <i class="fas fa-key"></i> Regenerate Key
                    </a>
                    <span style="font-size:0.72rem;color:var(--text-muted);">
                        <i class="fas fa-info-circle"></i> Old URL stops working immediately
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleApiKey() {
    var input = document.getElementById('apiKeyInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
