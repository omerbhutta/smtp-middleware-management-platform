# SMTP Management Platform

A web-based SMTP middleware management system with department-based security keys, suppression management, analytics, audit trails, MFA authentication, Microsoft OAuth2 login, and self-update capabilities.

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache with mod_rewrite (or nginx with equivalent rewrite rules)
- Extensions: PDO, pdo_mysql, curl, json, mbstring, openssl

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/smtp-management-platform.git
   cd smtp-management-platform
   ```

2. Point your web server to the project directory. Apache users: ensure `.htaccess` is enabled (`AllowOverride All`).

3. Open the application in your browser. You will be redirected to the installer:
   ```
   https://yourdomain.com/
   ```

4. Follow the installer steps:
   - **Step 1**: Enter database credentials and run the schema migration
   - **Step 2**: Create the admin user account
   - **Step 3**: Login with your new credentials

## Quick Start

1. **Login** with the admin credentials you created during installation
2. **Departments** → Create departments to organize your email sending
3. **Security Keys** → Generate API keys tied to each department
4. **SMTP Accounts** → Add your SMTP provider credentials
5. **API Endpoint** → Use `https://yourdomain.com/api/send` to send emails

## User Roles

The platform has two built-in roles:

### Admin
Full access to all features — manage users, SMTP accounts, departments, security keys, suppression lists, analytics, audit logs, system settings, and self-updates.

### User
Read-only access for monitoring and tracking:
- Dashboard, Email Activity, Analytics, Audit Logs, Documentation
- Suppression Logs — can **add** new entries but cannot remove existing ones
- All management screens (Users, SMTP Accounts, Departments, Security Keys, Suppression API, Self Update) are hidden

Role is assigned when creating/editing a user via **Users → Create/Edit User → Role** dropdown.

## Microsoft OAuth2 Login

Users can sign in with their Microsoft work/school accounts instead of using a username/password.

### Configuration

1. Go to **Azure Portal → App registrations → New registration**
2. Set the Redirect URI to `https://yourdomain.com/auth/microsoft/callback`
3. Note the **Application (client) ID** and generate a **Client Secret**
4. Navigate to **Users → Edit User → Microsoft Login Configuration**
5. Enter your Client ID, Client Secret, Tenant ID (`common` for multi-tenant), and Redirect URI
6. Click **Save MS Settings**

Users must have a matching email in the system — their Microsoft email must match an existing user account in the platform. When they click **Sign in with Microsoft** on the login page, they'll be authenticated via OAuth2 and logged in automatically.

## Self Update

The platform includes a built-in self-update feature accessible from the sidebar under **System → Self Update**.

- **Pull Latest**: Runs `git fetch` and `git pull` to update to the latest commit
- **Full Update**: Runs fetch + pull and notes that auto-migrations run on next page load
- All update attempts are logged in the **deploy_logs** table with status, output, and user attribution

## API Usage

```
POST /api/send
Content-Type: application/x-www-form-urlencoded

security=<api_key>&subject=Hello&to=user@example.com&body=<h1>Test</h1>&from=sender@example.com
```

### Parameters

| Parameter | Required | Description |
|-----------|----------|-------------|
| `security` | Yes | API key or secret key |
| `subject` | Yes | Email subject |
| `to` | Yes | Recipient email (comma-separated for multiple) |
| `body` | Yes | HTML email body |
| `from` | No | Sender email (defaults to SMTP account sender) |
| `bcc` | No | BCC recipients (comma-separated) |
| `attachmentURL` | No | URL of file to attach |
| `attachmentEncoding` | No | `base64` or `raw` |
| `attachmentType` | No | MIME type (e.g., `application/pdf`) |

### Response

```json
{"status": true, "message": "Email sent successfully"}
```

## Suppression Sync

Navigate to **Management → Suppression API** to configure an external suppression list. Copy the cron URL and set up a cron job service (cron-job.org, UptimeRobot, etc.) to call it every 5 minutes.

## URL Structure

The application uses clean URLs:
- `https://yourdomain.com/dashboard`
- `https://yourdomain.com/smtp_accounts`
- `https://yourdomain.com/smtp_accounts/edit?id=1`
- `https://yourdomain.com/api/send`

## License

Proprietary. All rights reserved.
