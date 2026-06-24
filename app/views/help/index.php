<style>
.nav-tabs-smm { display:flex; gap:0; border-bottom:2px solid var(--border-color); margin-bottom:24px; }
.nav-tab-smm { padding:10px 24px; font-size:0.85rem; font-weight:600; color:var(--text-muted); cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; transition:all 0.2s; background:none; border-top:0; border-left:0; border-right:0; }
.nav-tab-smm:hover { color:var(--text-primary); }
.nav-tab-smm.active { color:var(--blue-primary); border-bottom-color:var(--blue-primary); }
.tab-content-smm > .tab-pane-smm { display:none; }
.tab-content-smm > .tab-pane-smm.active { display:block; }
</style>

<div class="row">
    <div class="col-12">
        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3><i class="fas fa-book me-2" style="color:var(--cyan);"></i> Documentation</h3>
            </div>
            <div class="card-smm-body" style="line-height:1.8;font-size:0.88rem;color:var(--text-secondary);">

                <div class="nav-tabs-smm">
                    <button class="nav-tab-smm active" data-tab="manual">User Manual</button>
                    <button class="nav-tab-smm" data-tab="api">API Reference</button>
                </div>

                <!-- ========== TAB 1: USER MANUAL ========== -->
                <div class="tab-content-smm">
                    <div class="tab-pane-smm active" id="tab-manual">

                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Overview</h4>
                        <p>
                            The <strong>SMTP Management Platform</strong> (SMMP) is a web-based middleware system that sits between your
                            applications and your SMTP providers. It centralizes email sending across all your projects, providing
                            department-based access control, delivery monitoring, suppression management, and detailed analytics.
                        </p>
                        <p>
                            Instead of configuring SMTP credentials in every application, you generate a <strong>security key</strong>
                            once and use it in all your apps. The platform handles authentication, SMTP routing, bounce detection,
                            and logging.
                        </p>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Navigation -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">System Navigation</h4>
                        <div class="table-responsive table-wrap">
                            <table class="table-smm" style="font-size:0.82rem;">
                                <thead><tr><th>Menu</th><th>Page</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td><i class="fas fa-chart-bar" style="color:var(--blue-primary);"></i></td><td><strong>Dashboard</strong></td><td>Overview stats: emails sent today / week / month, success rate, recent activity feed</td></tr>
                                    <tr><td><i class="fas fa-envelope-open-text" style="color:var(--blue-primary);"></i></td><td><strong>Email Logs</strong></td><td>Complete history of all API requests with recipients, status, priority, attachments, CC/BCC</td></tr>
                                    <tr><td><i class="fas fa-server" style="color:var(--blue-primary);"></i></td><td><strong>SMTP Accounts</strong></td><td>Manage SMTP provider connections (Microsoft 365, Gmail, Custom SMTP)</td></tr>
                                    <tr><td><i class="fas fa-sitemap" style="color:var(--blue-primary);"></i></td><td><strong>Departments</strong></td><td>Organize projects/teams. Each department has its own security keys and SMTP accounts</td></tr>
                                    <tr><td><i class="fas fa-key" style="color:var(--blue-primary);"></i></td><td><strong>Security Keys</strong></td><td>Generate and revoke API keys and secret keys for application authentication</td></tr>
                                    <tr><td><i class="fas fa-ban" style="color:var(--blue-primary);"></i></td><td><strong>Suppression</strong></td><td>Manage bounced / blocked recipients and configure automatic suppression sync</td></tr>
                                    <tr><td><i class="fas fa-chart-line" style="color:var(--blue-primary);"></i></td><td><strong>Analytics</strong></td><td>Visual charts: daily volume, provider distribution, department activity, success rates</td></tr>
                                    <tr><td><i class="fas fa-clipboard-list" style="color:var(--blue-primary);"></i></td><td><strong>Audit Trail</strong></td><td>Log of all admin actions: logins, SMTP changes, key generation, suppression edits</td></tr>
                                    <tr><td><i class="fas fa-users" style="color:var(--blue-primary);"></i></td><td><strong>Users</strong></td><td>Manage admin users, roles, MFA settings</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Getting Started -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Getting Started</h4>

                        <h5 style="color:var(--text-primary);">1. Create a Department</h5>
                        <p>
                            Go to <strong>Management &rarr; Departments &rarr; New Department</strong>. Departments let you group
                            your email sending by team, project, or client. Each department operates independently with its own
                            SMTP accounts and security keys.
                        </p>

                        <h5 class="mt-4" style="color:var(--text-primary);">2. Add an SMTP Account</h5>
                        <p>
                            Go to <strong>Management &rarr; SMTP Accounts &rarr; Add SMTP</strong>. Enter your SMTP provider
                            details (host, port, username, password, encryption). You can assign it to a specific department
                            or leave it as <em>Shared</em> for all departments to use as fallback.
                        </p>
                        <ul style="padding-left:1.2rem;">
                            <li><strong>Standard SMTP</strong> — Used for API email sending</li>
                            <li><strong>Portal SMTP</strong> — Used exclusively for system emails (OTP codes, password resets). Only one account can be the portal SMTP</li>
                            <li><strong>Shared SMTP</strong> — No department assigned, usable by any department as fallback</li>
                        </ul>
                        <p>Use the <strong>Test Connection</strong> feature to verify SMTP credentials before saving.</p>

                        <h5 class="mt-4" style="color:var(--text-primary);">3. Generate a Security Key</h5>
                        <p>
                            Go to <strong>Management &rarr; Security Keys &rarr; Generate Key</strong>. Select the department
                            and generate the key. You'll get an <strong>API Key</strong> (primary) and a <strong>Secret Key</strong>
                            (fallback for legacy systems). Your applications use these to authenticate when calling the API.
                        </p>
                        <p>
                            The API checks the API Key first, then falls back to the Secret Key. Unknown keys are auto-created
                            under a <em>Legacy</em> department for backward compatibility.
                        </p>

                        <h5 class="mt-4" style="color:var(--text-primary);">4. Start Sending Emails</h5>
                        <p>
                            Use the <code>/api/send</code> endpoint with your security key. See the <strong>API Reference</strong>
                            tab for full documentation and examples.
                        </p>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Email Logs -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Email Logs</h4>
                        <p>
                            Every API request is logged with full details. Go to <strong>Email Logs</strong> to view the history.
                            Each entry shows:
                        </p>
                        <ul style="padding-left:1.2rem;">
                            <li>Recipients with delivery status (sent / skipped)</li>
                            <li>CC and BCC recipients</li>
                            <li>Priority level (High / Normal / Low)</li>
                            <li>Reply-To address</li>
                            <li>Attachment count and indicators</li>
                            <li>Department, sender, and API key used</li>
                            <li>Status (sent / failed) with error messages</li>
                            <li>Timestamp in your configured timezone</li>
                        </ul>
                        <p>Use filters by date range, department, status, and search to narrow results.</p>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Suppression -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Suppression Management</h4>
                        <p>
                            The suppression system automatically blocks sending to recipients who have previously bounced or
                            complained. This protects your sender reputation and reduces failed delivery attempts.
                        </p>

                        <h5 style="color:var(--text-primary);">Manual Suppression</h5>
                        <p>Go to <strong>Management &rarr; Suppression Logs</strong> to add or remove suppressed email addresses manually.</p>

                        <h5 class="mt-3" style="color:var(--text-primary);">Automatic Sync</h5>
                        <p>
                            Configure an external suppression API endpoint at <strong>Management &rarr; Suppression API</strong>.
                            The platform can periodically fetch suppressed emails from an external source and sync them into
                            the local database. Set up a cron job or use the provided web-accessible cron URL.
                        </p>

                        <h5 style="color:var(--text-primary);">How It Works</h5>
                        <p>When the API processes a send request:</p>
                        <ol style="padding-left:1.2rem;">
                            <li>Each recipient is checked against the suppression cache</li>
                            <li>Suppressed recipients are skipped and reported in the response</li>
                            <li>Only non-suppressed recipients receive the email</li>
                            <li>Delivery failures can automatically add recipients to the suppression list</li>
                        </ol>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Analytics -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Analytics</h4>
                        <p>
                            The Analytics dashboard provides visual insights into your email sending patterns:
                        </p>
                        <ul style="padding-left:1.2rem;">
                            <li><strong>Daily Traffic</strong> — Line chart showing emails sent per day over the last 30 days</li>
                            <li><strong>Provider Distribution</strong> — Donut chart of SMTP provider usage</li>
                            <li><strong>Department Activity</strong> — Bar chart of emails by department</li>
                            <li><strong>Success Rate</strong> — Pie chart of sent vs failed deliveries</li>
                            <li><strong>Day of Week &amp; Hourly Distribution</strong> — When your emails are being sent</li>
                            <li><strong>Failure Analysis</strong> — Top error messages breakdown</li>
                        </ul>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Audit Trail -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Audit Trail</h4>
                        <p>
                            Every significant action in the platform is logged to the audit trail for security and compliance:
                        </p>
                        <ul style="padding-left:1.2rem;">
                            <li>User logins (including MFA status and IP address)</li>
                            <li>SMTP account creation, updates, and deletion</li>
                            <li>Security key generation and revocation</li>
                            <li>Department management</li>
                            <li>Suppression list changes</li>
                            <li>Configuration changes</li>
                        </ul>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Authentication & Security -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Authentication &amp; Security</h4>

                        <h5 style="color:var(--text-primary);">Multi-Factor Authentication (MFA)</h5>
                        <p>
                            MFA is enabled by default for all admin users. When logging in, a one-time password (OTP) is
                            sent to your email via the Portal SMTP. If no Portal SMTP is configured, MFA is automatically skipped.
                        </p>

                        <h5 class="mt-3" style="color:var(--text-primary);">User Roles</h5>
                        <p>Two role levels:</p>
                        <ul style="padding-left:1.2rem;">
                            <li><strong>Admin</strong> — Full access to all features and settings</li>
                            <li><strong>User</strong> — Limited access based on assigned departments</li>
                        </ul>

                        <h5 class="mt-3" style="color:var(--text-primary);">Theme &amp; Preferences</h5>
                        <p>User preferences (dark/light theme, sidebar state) are persisted in the database and restored on each login.</p>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- SMTP Account Selection Logic -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">SMTP Account Selection Logic</h4>
                        <p>When the API processes an email, it selects the SMTP account in this order:</p>
                        <ol style="padding-left:1.2rem;">
                            <li>Looks for an active SMTP account assigned to the key's department</li>
                            <li>If not found, falls back to a shared SMTP account (no department assignment)</li>
                            <li>If still not found, returns an error</li>
                        </ol>
                        <p>Portal SMTP accounts (used for system emails like OTP and password resets) are excluded from API sending.</p>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <!-- Troubleshooting -->
                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Troubleshooting</h4>

                        <h5 style="color:var(--text-primary);">"No active SMTP account configured"</h5>
                        <ul style="padding-left:1.2rem;">
                            <li>Verify the security key is assigned to the correct department</li>
                            <li>Verify the department has an active SMTP account (or a shared SMTP account exists)</li>
                            <li>Ensure the SMTP account does <strong>not</strong> have "Use as Portal SMTP" checked</li>
                        </ul>

                        <h5 class="mt-3" style="color:var(--text-primary);">Email not delivered</h5>
                        <ul style="padding-left:1.2rem;">
                            <li>Check the email logs for error messages</li>
                            <li>Verify the recipient is not in the suppression list</li>
                            <li>Check SMTP account credentials and connection settings</li>
                            <li>Test the SMTP connection from the account edit page</li>
                        </ul>

                        <h5 class="mt-3" style="color:var(--text-primary);">MFA keeps failing / OTP not received</h5>
                        <ul style="padding-left:1.2rem;">
                            <li>Verify a Portal SMTP account is configured and active</li>
                            <li>Check the email logs for the OTP delivery attempt</li>
                            <li>If no Portal SMTP exists, MFA is skipped automatically</li>
                        </ul>

                    </div>

                    <!-- ========== TAB 2: API REFERENCE ========== -->
                    <div class="tab-pane-smm" id="tab-api">

                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">API Overview</h4>
                        <p>
                            The API lets your applications send emails through the platform using a simple HTTP POST request.
                            The platform handles SMTP routing, authentication, suppression checks, and logging.
                        </p>

                        <h5 style="color:var(--text-primary);">Authentication</h5>
                        <p>Every request requires a valid security key. Pass it as the <code>security</code> parameter.</p>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Endpoint</h4>
                        <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:8px 12px;font-family:'Courier New',monospace;font-size:0.82rem;color:var(--cyan);">
                            POST <?= BASE_URL ?>api/send
                        </div>
                        <p class="mt-2">Content-Type: <code>application/x-www-form-urlencoded</code> or <code>multipart/form-data</code></p>

                        <h5 class="mt-3" style="color:var(--text-primary);">Parameters</h5>
                        <div class="table-responsive table-wrap">
                            <table class="table-smm" style="font-size:0.82rem;">
                                <thead><tr><th>Parameter</th><th>Required</th><th>Description</th></tr></thead>
                                <tbody>
                                    <tr><td><code>security</code></td><td>Yes</td><td>API key or secret key</td></tr>
                                    <tr><td><code>subject</code></td><td>Yes</td><td>Email subject line</td></tr>
                                    <tr><td><code>to</code></td><td>Yes</td><td>Recipient email(s). Multiple: comma or semicolon separated</td></tr>
                                    <tr><td><code>body</code></td><td>Yes</td><td>HTML email body content</td></tr>
                                    <tr><td><code>from</code></td><td>Yes</td><td>Sender email address</td></tr>
                                    <tr><td><code>fromName</code></td><td>No</td><td>Sender display name</td></tr>
                                    <tr><td><code>cc</code></td><td>No</td><td>CC recipient(s). Multiple: comma or semicolon separated</td></tr>
                                    <tr><td><code>bcc</code></td><td>No</td><td>BCC recipient(s). Multiple: comma or semicolon separated</td></tr>
                                    <tr><td><code>replyTo</code></td><td>No</td><td>Reply-To email address</td></tr>
                                    <tr><td><code>priority</code></td><td>No</td><td>Email priority: <code>1</code> (High), <code>3</code> (Normal), <code>5</code> (Low)</td></tr>
                                    <tr><td colspan="3" style="border-top:2px solid var(--border-color);"></td></tr>
                                    <tr><td colspan="3" style="color:var(--text-primary);font-weight:600;">Attachments — URL Mode</td></tr>
                                    <tr><td><code>attachmentURL</code></td><td>No</td><td>URL(s) of file(s) to attach. Multiple: comma or semicolon separated</td></tr>
                                    <tr><td><code>attachmentType</code></td><td>No</td><td>MIME type(s). Multiple values map to each URL (e.g. <code>application/pdf,image/png</code>)</td></tr>
                                    <tr><td><code>attachmentEncoding</code></td><td>No</td><td>Encoding: <code>base64</code> (default) or <code>raw</code></td></tr>
                                    <tr><td colspan="3" style="border-top:2px solid var(--border-color);"></td></tr>
                                    <tr><td colspan="3" style="color:var(--text-primary);font-weight:600;">Attachments — Binary Data Mode</td></tr>
                                    <tr><td><code>attachmentData</code></td><td>No</td><td>Base64-encoded file content(s). Pass as array for multiple (<code>attachmentData[]</code>)</td></tr>
                                    <tr><td><code>attachmentName</code></td><td>No</td><td>Filename(s) for the attachment. Pass as array for multiple</td></tr>
                                    <tr><td><code>attachmentDataEncoding</code></td><td>No</td><td>Encoding of <code>attachmentData</code>: <code>base64</code> (default) or <code>raw</code></td></tr>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mt-3" style="color:var(--text-primary);">Response Format</h5>
                        <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:8px 12px;font-family:'Courier New',monospace;font-size:0.82rem;">
                            <div style="color:var(--emerald);">{"status": true, "message": "Email sent successfully"}</div>
                            <div style="color:var(--rose);margin-top:4px;">{"status": false, "message": "No active SMTP account configured for this department"}</div>
                        </div>

                        <h5 class="mt-3" style="color:var(--text-primary);">Error Responses</h5>
                        <div class="table-responsive table-wrap">
                            <table class="table-smm" style="font-size:0.82rem;">
                                <thead><tr><th>Response</th><th>Cause</th></tr></thead>
                                <tbody>
                                    <tr><td><code>Bad request</code></td><td>Missing or empty security key</td></tr>
                                    <tr><td><code>Invalid security key</code></td><td>Security key not found in database</td></tr>
                                    <tr><td><code>No active SMTP account configured for this department</code></td><td>No active SMTP account is assigned to the key's department (or shared)</td></tr>
                                    <tr><td><code>...is a Test/Wrong/Invalid Email Address</code></td><td>Recipient is in the forbidden emails list</td></tr>
                                    <tr><td><code>...is suppressed due to previous delivery failures</code></td><td>Recipient is in the suppression cache</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">cURL Examples</h4>

                        <h5 style="color:var(--text-primary);">Basic Email</h5>
                        <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                            <div style="color:var(--text-secondary);">
                                curl -X POST <?= BASE_URL ?>api/send \<br>
                                &nbsp;&nbsp;-d "security=your_api_key" \<br>
                                &nbsp;&nbsp;-d "subject=Hello" \<br>
                                &nbsp;&nbsp;-d "to=user@example.com" \<br>
                                &nbsp;&nbsp;-d "body=&lt;h1&gt;Test&lt;/h1&gt;" \<br>
                                &nbsp;&nbsp;-d "from=sender@example.com"
                            </div>
                        </div>

                        <h5 class="mt-3" style="color:var(--text-primary);">With CC, BCC, Priority, Reply-To</h5>
                        <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                            <div style="color:var(--text-secondary);">
                                curl -X POST <?= BASE_URL ?>api/send \<br>
                                &nbsp;&nbsp;-d "security=your_api_key" \<br>
                                &nbsp;&nbsp;-d "subject=Urgent: Report" \<br>
                                &nbsp;&nbsp;-d "to=user@example.com" \<br>
                                &nbsp;&nbsp;-d "body=&lt;h1&gt;Report&lt;/h1&gt;" \<br>
                                &nbsp;&nbsp;-d "from=sender@example.com" \<br>
                                &nbsp;&nbsp;-d "cc=manager@example.com" \<br>
                                &nbsp;&nbsp;-d "bcc=archive@example.com" \<br>
                                &nbsp;&nbsp;-d "replyTo=noreply@example.com" \<br>
                                &nbsp;&nbsp;-d "priority=1"
                            </div>
                        </div>

                        <h5 class="mt-3" style="color:var(--text-primary);">With URL Attachment</h5>
                        <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                            <div style="color:var(--text-secondary);">
                                curl -X POST <?= BASE_URL ?>api/send \<br>
                                &nbsp;&nbsp;-d "security=your_api_key" \<br>
                                &nbsp;&nbsp;-d "subject=Invoice" \<br>
                                &nbsp;&nbsp;-d "to=user@example.com" \<br>
                                &nbsp;&nbsp;-d "body=&lt;p&gt;See attached&lt;/p&gt;" \<br>
                                &nbsp;&nbsp;-d "from=sender@example.com" \<br>
                                &nbsp;&nbsp;-d "attachmentURL=https://example.com/invoice.pdf" \<br>
                                &nbsp;&nbsp;-d "attachmentType=application/pdf"
                            </div>
                        </div>

                        <h5 class="mt-3" style="color:var(--text-primary);">With Binary Attachment (Base64)</h5>
                        <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                            <div style="color:var(--text-secondary);">
                                curl -X POST <?= BASE_URL ?>api/send \<br>
                                &nbsp;&nbsp;-d "security=your_api_key" \<br>
                                &nbsp;&nbsp;-d "subject=Report" \<br>
                                &nbsp;&nbsp;-d "to=user@example.com" \<br>
                                &nbsp;&nbsp;-d "body=&lt;p&gt;See attached&lt;/p&gt;" \<br>
                                &nbsp;&nbsp;-d "from=sender@example.com" \<br>
                                &nbsp;&nbsp;-d "attachmentData=$(base64 -w0 report.pdf)" \<br>
                                &nbsp;&nbsp;-d "attachmentName=report.pdf" \<br>
                                &nbsp;&nbsp;-d "attachmentType=application/pdf"
                            </div>
                        </div>

                        <h5 class="mt-3" style="color:var(--text-primary);">Multiple Attachments (URL + Binary)</h5>
                        <div class="code-block" style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:6px;padding:12px;font-family:'Courier New',monospace;font-size:0.78rem;overflow-x:auto;">
                            <div style="color:var(--text-secondary);">
                                curl -X POST <?= BASE_URL ?>api/send \<br>
                                &nbsp;&nbsp;-d "security=your_api_key" \<br>
                                &nbsp;&nbsp;-d "subject=Documents" \<br>
                                &nbsp;&nbsp;-d "to=user@example.com" \<br>
                                &nbsp;&nbsp;-d "body=&lt;p&gt;Multiple files&lt;/p&gt;" \<br>
                                &nbsp;&nbsp;-d "from=sender@example.com" \<br>
                                &nbsp;&nbsp;-d "attachmentURL=https://example.com/a.pdf,https://example.com/b.pdf" \<br>
                                &nbsp;&nbsp;-d "attachmentType=application/pdf,image/png" \<br>
                                &nbsp;&nbsp;-d "attachmentData[]=$(base64 -w0 extra.pdf)" \<br>
                                &nbsp;&nbsp;-d "attachmentName[]=extra.pdf"
                            </div>
                        </div>

                        <hr class="my-4" style="border-color:var(--border-color);">

                        <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">API Best Practices</h4>
                        <ul style="padding-left:1.2rem;">
                            <li>Store the security key in environment variables, never hardcode it</li>
                            <li>Use the <code>attachmentData</code> mode for dynamically generated files instead of writing them to disk</li>
                            <li>Set <code>priority=1</code> for time-sensitive emails (password resets, alerts)</li>
                            <li>Use <code>replyTo</code> when the sender address should not receive replies</li>
                            <li>Monitor suppressed recipients and update your external suppression list regularly</li>
                            <li>Check email logs after deployment to verify delivery</li>
                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.nav-tab-smm').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.nav-tab-smm').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.tab-pane-smm').forEach(function(p) { p.classList.remove('active'); });
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});
</script>
