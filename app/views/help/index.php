<div class="row">
    <div class="col-12">
        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3><i class="fas fa-book me-2" style="color:var(--cyan);"></i> Documentation</h3>
            </div>
            <div class="card-smm-body" style="line-height:1.8;font-size:0.88rem;color:var(--text-secondary);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Overview</h4>
                <p>
                    The <strong>SMTP Management Platform</strong> (SMMP) is a web-based middleware system that sits between your
                    applications and your SMTP providers. It provides department-based security keys, suppression management,
                    delivery analytics, audit trails, and MFA-protected administration.
                </p>
                <p>
                    Applications send emails via the API endpoint using a security key. The platform validates the key,
                    looks up the associated SMTP account, checks suppression lists, sends the email, and logs every
                    attempt for auditing and analytics.
                </p>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Architecture</h4>
                <div class="table-responsive table-wrap">
                    <table class="table-smm" style="font-size:0.82rem;">
                        <thead><tr><th>Component</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>index.php</code></td><td>Front controller — loads .env, initializes DB, routes requests</td></tr>
                            <tr><td><code>.htaccess</code></td><td>Apache mod_rewrite — clean URLs, routes to front controller</td></tr>
                            <tr><td><code>app/controllers/</code></td><td>MVC controllers — handle HTTP requests, business logic</td></tr>
                            <tr><td><code>app/models/</code></td><td>MVC models — database queries and data access</td></tr>
                            <tr><td><code>app/views/</code></td><td>MVC views — HTML templates with embedded PHP</td></tr>
                            <tr><td><code>app/services/</code></td><td>Service classes — authentication, SMTP mailing, database connection</td></tr>
                            <tr><td><code>config/</code></td><td>Application and database configuration</td></tr>
                            <tr><td><code>api/send.php</code></td><td>Backward-compatible email sending API endpoint</td></tr>
                            <tr><td><code>vendor/phpmailer/</code></td><td>PHPMailer library for SMTP sending</td></tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Installation</h4>
                <ol style="padding-left:1.2rem;">
                    <li class="mb-2">Clone the repository and copy <code>.env.example</code> to <code>.env</code></li>
                    <li class="mb-2">Edit <code>.env</code> with your database credentials (host, port, database name, username, password)</li>
                    <li class="mb-2">Ensure Apache mod_rewrite is enabled and <code>AllowOverride All</code> is set for the directory</li>
                    <li class="mb-2">Open the app in your browser — you will be redirected to the installer</li>
                    <li class="mb-2">The installer will:
                        <ul>
                            <li>Verify system requirements (PHP 8.0+, PDO, cURL, etc.)</li>
                            <li>Create the database and all required tables</li>
                            <li>Create the admin user account</li>
                            <li>Write the <code>.env</code> file with your configuration</li>
                            <li>Create a lock file to prevent re-installation</li>
                        </ul>
                    </li>
                </ol>

                <p class="mt-2">After installation, log in with the admin credentials you created. Default: <code>admin</code> / password you chose.</p>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Quick Start Guide</h4>

                <h5 class="mt-4" style="color:var(--text-primary);">1. Create a Department</h5>
                <p>Go to <strong>Management &rarr; Departments &rarr; New Department</strong>. Departments group your email sending by team or project. Each department gets its own SMTP accounts and security keys.</p>

                <h5 class="mt-4" style="color:var(--text-primary);">2. Generate a Security Key</h5>
                <p>Go to <strong>Management &rarr; Security Keys &rarr; Generate Key</strong>. Select the department and generate an API key and secret key. Your applications will use these keys to authenticate when sending emails via the API.</p>

                <h5 class="mt-4" style="color:var(--text-primary);">3. Add an SMTP Account</h5>
                <p>Go to <strong>Management &rarr; SMTP Accounts &rarr; Add SMTP</strong>. Enter your SMTP provider details (host, port, credentials, encryption). You can assign it to a specific department or leave it as <em>All Departments (Shared)</em> for global use.</p>

                <ul style="padding-left:1.2rem;">
                    <li><strong>Shared SMTP</strong> — no department assigned, usable by all departments as fallback</li>
                    <li><strong>Portal SMTP</strong> — used exclusively for system emails (MFA OTP, password resets)</li>
                    <li><strong>Test Connection</strong> — send a test email to verify SMTP credentials before saving</li>
                </ul>

                <h5 class="mt-4" style="color:var(--text-primary);">4. Send an Email via API</h5>
                <p>Use the API endpoint to send emails from your applications:</p>

                <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                    <div style="color:var(--text-muted);margin-bottom:6px;">POST /api/send</div>
                    <div style="color:var(--text-muted);margin-bottom:6px;">Content-Type: application/x-www-form-urlencoded</div>
                    <div style="color:var(--emerald);">
                        security=your_api_key&<br>
                        subject=Hello%20World&<br>
                        to=recipient@example.com&<br>
                        body=%3Ch1%3ETest%3C%2Fh1%3E&<br>
                        from=sender@example.com
                    </div>
                </div>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">API Reference</h4>

                <h5 style="color:var(--text-primary);">Endpoint</h5>
                <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:8px 12px;font-family:'Courier New',monospace;font-size:0.82rem;color:var(--cyan);">
                    POST https://yourdomain.com/api/send
                </div>

                <h5 class="mt-3" style="color:var(--text-primary);">Parameters</h5>
                <div class="table-responsive table-wrap">
                    <table class="table-smm" style="font-size:0.82rem;">
                        <thead><tr><th>Parameter</th><th>Required</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>security</code></td><td>Yes</td><td>API key or secret key assigned to a department</td></tr>
                            <tr><td><code>subject</code></td><td>Yes</td><td>Email subject line</td></tr>
                            <tr><td><code>to</code></td><td>Yes</td><td>Recipient email(s). Comma-separated for multiple recipients</td></tr>
                            <tr><td><code>body</code></td><td>Yes</td><td>HTML email body content</td></tr>
                            <tr><td><code>from</code></td><td>No</td><td>Sender email address. Defaults to the SMTP account's configured sender</td></tr>
                            <tr><td><code>bcc</code></td><td>No</td><td>BCC recipient(s). Comma-separated</td></tr>
                            <tr><td><code>attachmentURL</code></td><td>No</td><td>URL of a file to attach to the email</td></tr>
                            <tr><td><code>attachmentEncoding</code></td><td>No</td><td>Encoding of the attachment: <code>base64</code> (default) or <code>raw</code></td></tr>
                            <tr><td><code>attachmentType</code></td><td>No</td><td>MIME type of the attachment (e.g., <code>application/pdf</code>)</td></tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-3" style="color:var(--text-primary);">Response</h5>
                <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:8px 12px;font-family:'Courier New',monospace;font-size:0.82rem;">
                    <div style="color:var(--emerald);">{"status": true, "message": "Email sent successfully"}</div>
                    <div style="color:var(--rose);margin-top:4px;">{"status": false, "message": "No active SMTP account configured for this department"}</div>
                </div>

                <h5 class="mt-3" style="color:var(--text-primary);">Error Codes</h5>
                <div class="table-responsive table-wrap">
                    <table class="table-smm" style="font-size:0.82rem;">
                        <thead><tr><th>Response</th><th>Cause</th></tr></thead>
                        <tbody>
                            <tr><td><code>Bad request</code></td><td>Missing or empty security key</td></tr>
                            <tr><td><code>Invalid security key</code></td><td>Security key not found in database</td></tr>
                            <tr><td><code>No active SMTP account configured for this department</code></td><td>No SMTP account is active and assigned to the key's department (or shared)</td></tr>
                            <tr><td><code>...is a Test/Wrong/Invalid Email Address</code></td><td>Recipient is in the forbidden emails list</td></tr>
                            <tr><td><code>...is suppressed due to previous delivery failures</code></td><td>Recipient is in the suppression cache</td></tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-3" style="color:var(--text-primary);">cURL Example</h5>
                <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                    <div style="color:var(--text-secondary);">
                        curl -X POST https://yourdomain.com/api/send \<br>
                        &nbsp;&nbsp;-d "security=your_api_key" \<br>
                        &nbsp;&nbsp;-d "subject=Hello" \<br>
                        &nbsp;&nbsp;-d "to=user@example.com" \<br>
                        &nbsp;&nbsp;-d "body=<h1>Test</h1>" \<br>
                        &nbsp;&nbsp;-d "from=sender@example.com"
                    </div>
                </div>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">SMTP Account Management</h4>
                <p>
                    Each SMTP account stores the connection details for an email provider. The platform supports
                    <strong>Microsoft 365</strong>, <strong>Gmail</strong>, and <strong>Custom SMTP</strong> providers.
                </p>

                <h5 style="color:var(--text-primary);">Account Types</h5>
                <ul style="padding-left:1.2rem;">
                    <li><strong>Standard SMTP</strong> — Used for sending API emails. Can be assigned to a specific department or shared across all departments.</li>
                    <li><strong>Portal SMTP</strong> — Dedicated for system-generated emails: MFA OTP codes, password resets, and other admin notifications. Only one SMTP account can be the portal SMTP.</li>
                </ul>

                <h5 class="mt-3" style="color:var(--text-primary);">Fallback Logic</h5>
                <p>When the API processes an email request:</p>
                <ol style="padding-left:1.2rem;">
                    <li>Looks for an active SMTP account assigned to the key's department</li>
                    <li>If not found, falls back to a shared SMTP account (no department assignment)</li>
                    <li>If still not found, returns an error</li>
                </ol>

                <h5 class="mt-3" style="color:var(--text-primary);">Testing</h5>
                <p>On the SMTP account edit page, use the <strong>Test SMTP Connection</strong> card to send a test email to any address. This verifies the SMTP credentials, host, and port are working before you use the account in production.</p>

                <hr class="my-4" style="color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Security Keys</h4>
                <p>
                    Security keys authenticate your applications when calling the API. Each key is tied to a department
                    and has two values:
                </p>
                <ul style="padding-left:1.2rem;">
                    <li><strong>API Key</strong> — The primary key used in API requests (<code>security</code> parameter)</li>
                    <li><strong>Secret Key</strong> — Secondary key for backward compatibility with legacy systems</li>
                </ul>
                <p>
                    The API checks the API key first, then falls back to the secret key. Legacy keys that don't exist
                    in the database are auto-created under a <em>Legacy</em> department for backward compatibility.
                </p>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Suppression Management</h4>
                <p>
                    The suppression system prevents sending emails to recipients who have previously bounced or
                    complained. This protects your sender reputation and reduces failed delivery attempts.
                </p>

                <h5 style="color:var(--text-primary);">Manual Suppression</h5>
                <p>Go to <strong>Management &rarr; Suppression Logs</strong> to add or remove suppressed email addresses manually.</p>

                <h5 class="mt-3" style="color:var(--text-primary);">Automatic Sync</h5>
                <p>
                    Configure an external suppression API endpoint at <strong>Management &rarr; Suppression API</strong>.
                    The platform can periodically fetch a list of suppressed emails from an external source and
                    sync them into the local database.
                </p>

                <h5 class="mt-3" style="color:var(--text-primary);">Cron Job Setup</h5>
                <p>
                    The Suppression API settings page provides a web-accessible cron URL. Set up a cron job service
                    (cron-job.org, UptimeRobot, etc.) to hit this URL every 5 minutes. The URL is secured by
                    an auto-generated 32-character hex key.
                </p>
                <p>
                    For server-based crontabs, use the CLI fallback script:
                </p>
                <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:8px 12px;font-family:'Courier New',monospace;font-size:0.78rem;">
                    <span style="color:var(--text-secondary);">*/5 * * * * php /path/to/cron/suppression_sync.php</span>
                </div>

                <hr class="my-4" style="color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Analytics</h4>
                <p>
                    The Analytics dashboard provides visual insights into your email sending patterns:
                </p>
                <ul style="padding-left:1.2rem;">
                    <li><strong>Daily Traffic</strong> — Line chart showing emails sent per day</li>
                    <li><strong>Provider Distribution</strong> — Donut chart of SMTP provider usage</li>
                    <li><strong>Department Activity</strong> — Bar chart of emails by department</li>
                    <li><strong>Success Rate</strong> — Pie chart of sent vs failed deliveries</li>
                </ul>

                <hr class="my-4" style="color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Audit Trail</h4>
                <p>
                    Every significant action in the platform is logged to the audit trail:
                </p>
                <ul style="padding-left:1.2rem;">
                    <li>User logins (including MFA status)</li>
                    <li>SMTP account creation, updates, and deletion</li>
                    <li>Security key generation and revocation</li>
                    <li>Department management</li>
                    <li>Suppression list changes</li>
                    <li>Configuration changes</li>
                </ul>

                <hr class="my-4" style="color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Authentication &amp; Security</h4>

                <h5 style="color:var(--text-primary);">Multi-Factor Authentication (MFA)</h5>
                <p>
                    MFA is enabled by default for all admin users. When logging in, a one-time password (OTP) is
                    sent to the user's email address via the Portal SMTP. If no Portal SMTP is configured, MFA is
                    automatically skipped.
                </p>

                <h5 class="mt-3" style="color:var(--text-primary);">Theme &amp; Preferences</h5>
                <p>
                    User preferences (dark/light theme, sidebar state) are persisted in the database and restored
                    on each login, even from different devices.
                </p>

                <h5 class="mt-3" style="color:var(--text-primary);">Security Best Practices</h5>
                <ul style="padding-left:1.2rem;">
                    <li>Use strong, unique security keys for each department</li>
                    <li>Regularly review the audit log for unauthorized access attempts</li>
                    <li>Configure the suppression API to automatically block bounces</li>
                    <li>Rotate SMTP account passwords periodically</li>
                    <li>Keep the <code>.env</code> file secure and never commit it to version control</li>
                </ul>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">URL Structure</h4>
                <p>The application uses clean URLs via Apache mod_rewrite:</p>
                <div class="table-responsive table-wrap">
                    <table class="table-smm" style="font-size:0.82rem;">
                        <thead><tr><th>URL</th><th>Page</th></tr></thead>
                        <tbody>
                            <tr><td><code>/dashboard</code></td><td>Dashboard with stats and activity feed</td></tr>
                            <tr><td><code>/email_logs</code></td><td>Email sending history</td></tr>
                            <tr><td><code>/smtp_accounts</code></td><td>SMTP account management</td></tr>
                            <tr><td><code>/departments</code></td><td>Department management</td></tr>
                            <tr><td><code>/security_keys</code></td><td>Security key management</td></tr>
                            <tr><td><code>/suppression</code></td><td>Suppression list management</td></tr>
                            <tr><td><code>/analytics</code></td><td>Analytics dashboard</td></tr>
                            <tr><td><code>/audit</code></td><td>Audit trail</td></tr>
                            <tr><td><code>/auth/login</code></td><td>Login page</td></tr>
                            <tr><td><code>/auth/logout</code></td><td>Logout</td></tr>
                            <tr><td><code>/help</code></td><td>Documentation (this page)</td></tr>
                            <tr><td><code>/api/send</code></td><td>Email sending API endpoint</td></tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Configuration Reference</h4>

                <h5 style="color:var(--text-primary);">.env File</h5>
                <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                    <div style="color:var(--text-muted);"># Database Configuration</div>
                    <div style="color:var(--emerald);">DB_HOST</div><span style="color:var(--text-secondary);">=localhost</span>
                    <div style="color:var(--emerald);">DB_PORT</div><span style="color:var(--text-secondary);">=3306</span>
                    <div style="color:var(--emerald);">DB_DATABASE</div><span style="color:var(--text-secondary);">=smmp_smtp</span>
                    <div style="color:var(--emerald);">DB_USERNAME</div><span style="color:var(--text-secondary);">=root</span>
                    <div style="color:var(--emerald);">DB_PASSWORD</div><span style="color:var(--text-secondary);">=your_password</span>
                    <div style="color:var(--text-muted); margin-top:6px;"># Application Settings</div>
                    <div style="color:var(--emerald);">APP_NAME</div><span style="color:var(--text-secondary);">="SMTP Management Platform"</span>
                    <div style="color:var(--emerald);">APP_TIMEZONE</div><span style="color:var(--text-secondary);">=UTC</span>
                    <div style="color:var(--emerald);">APP_DEBUG</div><span style="color:var(--text-secondary);">=false</span>
                    <div style="color:var(--text-muted); margin-top:6px;"># Session</div>
                    <div style="color:var(--emerald);">SESSION_NAME</div><span style="color:var(--text-secondary);">=SMMP_SESSION</span>
                </div>

                <h5 class="mt-3" style="color:var(--text-primary);">System Settings (Database)</h5>
                <p>Configured via the UI, stored in the <code>system_settings</code> table:</p>
                <ul style="padding-left:1.2rem;">
                    <li><code>app_name</code> — Application display name</li>
                    <li><code>app_timezone</code> — Application timezone</li>
                    <li><code>forbidden_emails</code> — JSON array of email addresses blocked from sending</li>
                    <li><code>suppression_api_endpoint</code> — External API URL for suppression sync</li>
                    <li><code>suppression_api_key</code> — API key for suppression sync</li>
                    <li><code>suppression_api_method</code> — HTTP method (GET/POST) for suppression sync</li>
                    <li><code>cron_key</code> — Auto-generated key for cron job authentication</li>
                    <li><code>portal_smtp_id</code> — SMTP account ID used for system emails</li>
                </ul>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Troubleshooting</h4>

                <h5 style="color:var(--text-primary);">API returns "No active SMTP account configured"</h5>
                <ul style="padding-left:1.2rem;">
                    <li>Verify the security key is assigned to the correct department</li>
                    <li>Verify the department has an active SMTP account (or a shared SMTP account exists)</li>
                    <li>Ensure the SMTP account has <strong>Use as Portal SMTP</strong> unchecked (portal accounts are excluded from API sending)</li>
                </ul>

                <h5 class="mt-3" style="color:var(--text-primary);">MFA keeps failing / OTP not received</h5>
                <ul style="padding-left:1.2rem;">
                    <li>Verify a Portal SMTP account is configured and active</li>
                    <li>Check the email logs for the OTP delivery attempt</li>
                    <li>If no Portal SMTP exists, MFA is skipped automatically — ensure the user account has <code>mfa_enabled = 1</code></li>
                </ul>

                <h5 class="mt-3" style="color:var(--text-primary);">Clean URLs not working</h5>
                <ul style="padding-left:1.2rem;">
                    <li>Ensure Apache mod_rewrite is enabled: <code>sudo a2enmod rewrite</code></li>
                    <li>Ensure <code>AllowOverride All</code> is set in your Apache virtual host configuration</li>
                    <li>Verify the <code>.htaccess</code> file is present in the project root</li>
                </ul>

                <h5 class="mt-3" style="color:var(--text-primary);">Installation fails with "Access denied"</h5>
                <ul style="padding-left:1.2rem;">
                    <li>Use the <strong>Test Connection</strong> button to validate credentials before proceeding</li>
                    <li>Verify MySQL is running on the specified host and port</li>
                    <li>Ensure the database user has CREATE DATABASE privileges</li>
                </ul>

            </div>
        </div>
    </div>
</div>
