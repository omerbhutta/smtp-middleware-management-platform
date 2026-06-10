# SMTP Management Platform

A web-based SMTP middleware management system with department-based security keys, suppression management, analytics, audit trails, and MFA authentication.

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

2. Copy `.env.example` to `.env` and configure your database credentials:
   ```
   cp .env.example .env
   ```
   Edit `.env` with your database host, username, password, and database name.

3. Point your web server to the project directory. Apache users: ensure `.htaccess` is enabled (`AllowOverride All`).

4. Open the application in your browser. You will be redirected to the installer:
   ```
   https://yourdomain.com/install
   ```

5. Follow the installer steps:
   - **Step 1**: Enter database credentials and run the schema migration
   - **Step 2**: Create the admin user account
   - **Step 3**: Login with your new credentials

## Default Security Keys

The installer creates two legacy security keys for backward compatibility with existing systems. These are stored in the database and can be managed via the **Security Keys** section in the admin panel.

## Quick Start

1. **Login** with the admin credentials you created during installation
2. **Departments** → Create departments to organize your email sending
3. **Security Keys** → Generate API keys tied to each department
4. **SMTP Accounts** → Add your SMTP provider credentials
5. **API Endpoint** → Use `https://yourdomain.com/api/send` to send emails

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
