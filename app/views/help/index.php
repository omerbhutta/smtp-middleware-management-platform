<div class="row">
    <div class="col-12">
        <div class="card-smm animate-fade-up">
            <div class="card-smm-header">
                <h3><i class="fas fa-book me-2" style="color:var(--cyan);"></i> API Documentation</h3>
            </div>
            <div class="card-smm-body" style="line-height:1.8;font-size:0.88rem;color:var(--text-secondary);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Overview</h4>
                <p>
                    The <strong>SMTP Management Platform</strong> (SMMP) is a middleware API that sits between your
                    applications and your SMTP providers. It provides department-based security keys, suppression management,
                    delivery logging, and analytics.
                </p>
                <p>
                    Applications send emails via the API endpoint using a security key. The platform validates the key,
                    looks up the associated SMTP account, checks suppression lists, sends the email, and logs every
                    attempt.
                </p>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Authentication</h4>
                <p>Each API request requires a valid security key. Keys are generated in the admin panel under <strong>Management &rarr; Security Keys</strong> and are tied to a department.</p>
                <p>Pass the key as the <code>security</code> parameter. The API checks the <strong>API Key</strong> first, then falls back to the <strong>Secret Key</strong> for backward compatibility.</p>

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
                            <tr><td><code>to</code></td><td>Yes</td><td>Recipient email(s). Multiple recipients: comma or semicolon separated</td></tr>
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

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">SMTP Account Selection</h4>
                <p>When the API processes an email, it selects the SMTP account in this order:</p>
                <ol style="padding-left:1.2rem;">
                    <li>Looks for an active SMTP account assigned to the key's department</li>
                    <li>If not found, falls back to a shared SMTP account (no department assignment)</li>
                    <li>If still not found, returns an error</li>
                </ol>
                <p>Portal SMTP accounts (used for system emails like OTP and password resets) are excluded from API sending.</p>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Suppression</h4>
                <p>
                    The suppression system automatically blocks sending to recipients who have previously bounced or
                    complained. If a recipient is suppressed, the API skips them and returns their email in the response.
                </p>
                <p>Manage suppressions manually under <strong>Management &rarr; Suppression Logs</strong> or configure automatic sync from an external API.</p>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Email Logs</h4>
                <p>
                    Every API request is logged with full details: recipients, CC, BCC, sender, subject, attachments,
                    status, and error messages. View logs under <strong>Email Logs</strong> in the admin panel.
                </p>

                <hr class="my-4" style="border-color:var(--border-color);">

                <h4 class="mb-3" style="color:var(--text-primary);font-weight:600;">Troubleshooting</h4>

                <h5 style="color:var(--text-primary);">"No active SMTP account configured"</h5>
                <ul style="padding-left:1.2rem;">
                    <li>Verify the security key is assigned to the correct department</li>
                    <li>Verify the department has an active SMTP account (or a shared SMTP account exists)</li>
                    <li>Ensure the SMTP account does <strong>not</strong> have "Use as Portal SMTP" checked</li>
                </ul>

                <h5 class="mt-3" style="color:var(--text-primary);">Attachment not received</h5>
                <ul style="padding-left:1.2rem;">
                    <li>For URL attachments: ensure the URL is publicly accessible</li>
                    <li>For binary data: ensure the data is properly base64-encoded</li>
                    <li>Check file size limits (PHP <code>upload_max_filesize</code> / <code>post_max_size</code>)</li>
                </ul>

                <h5 class="mt-3" style="color:var(--text-primary);">Email not delivered</h5>
                <ul style="padding-left:1.2rem;">
                    <li>Check the email logs for error messages</li>
                    <li>Verify the recipient is not in the suppression list</li>
                    <li>Check SMTP account credentials and connection settings</li>
                </ul>

            </div>
        </div>
    </div>
</div>
