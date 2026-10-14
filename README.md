# Sembark URL Shortener

A multi-tenant URL shortening web application built with Laravel 12, MySQL, Tailwind CSS, and Alpine.js for the Sembark technical assessment.

The application implements company-level data isolation, a 3-tier role hierarchy (Super Admin, Company Admin, Member), tokenized email invitations, short link management with click tracking, and automated tests.

---

## Features

- **Multi-Tenant Isolation**: Links, team members, and analytics belong strictly to each company.
- **Role-Based Access Control**:
  - **Super Admin**: Manages companies, invites Company Admins, and views platform-wide metrics. Restricted from creating short URLs.
  - **Company Admin**: Manages company links, views company-wide analytics, and invites team members or other admins.
  - **Member**: Creates short links and views personal click analytics.
- **Raw SQL Seeder**: The default Super Admin account is seeded using parameterized raw SQL (`DB::insert`) as required by the assignment specs.
- **Tokenized Invitations**: Secure 7-day invite links sent via email. Tokens are stored as SHA-256 hashes in the database.
- **Fast Redirection & Click Tracking**: `/r/{code}` redirects to the original URL while incrementing total clicks.
- **Dashboard & CSV Export**: Filter link performance by date ranges (`Today`, `Last Week`, `This Month`, `Last Month`, `All Time`) with CSV export support.

---

## Tech Stack & Requirements

- **PHP**: 8.2+
- **Laravel**: 12.x
- **Database**: MySQL 8.0+ (or MariaDB / SQLite)
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Vite
- **Composer**: 2.x
- **Node.js**: 18+ & npm

---

## Quick Setup

### 1. Clone the repository
```bash
git clone https://github.com/imdeepaksuthar/sembark-url-shortener.git
cd sembark-url-shortener
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Set up environment variables
```bash
cp .env.example .env
php artisan key:generate
```
*(On Windows PowerShell, use `copy .env.example .env`)*

### 4. Configure database and credentials
Update `.env` with your database credentials:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sembark_url_shortener
DB_USERNAME=root
DB_PASSWORD=

SUPER_ADMIN_EMAIL=superadmin@example.com
SUPER_ADMIN_PASSWORD=password

# Log emails locally to storage/logs/laravel.log:
MAIL_MAILER=log
```

> **Note**: Make sure the `sembark_url_shortener` database exists in MySQL before running migrations (`CREATE DATABASE sembark_url_shortener;`).

### 5. Run migrations & seeders
```bash
php artisan migrate --seed
```
This runs migrations and executes `SuperAdminSeeder` to create the Super Admin user via raw SQL.

### 6. Build assets & start dev server
In one terminal, compile the frontend assets:
```bash
npm run dev
# or for a production build: npm run build
```

In another terminal, start the Laravel server:
```bash
php artisan serve
```

The app will be available at **http://localhost:8000**.

---

## Default Login

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `superadmin@example.com` | `password` |

### Testing Company & Member flows:
1. Log in as `superadmin@example.com`.
2. Click **"+ Invite Client"** on the dashboard to create a new company and invite a Company Admin.
3. Check `storage/logs/laravel.log` to grab the invitation link (e.g. `http://localhost:8000/invitations/<token>`).
4. Open the link to set a password and activate the Company Admin account.
5. Once in the company dashboard, use **"+ Invite Member"** to onboard team members or start generating short URLs.

---

## Role Permissions Matrix

| Action | Super Admin | Company Admin | Member |
|---|:---:|:---:|:---:|
| Create companies & invite Company Admins | Yes | No | No |
| Invite team admins & members | No | Yes (own company) | No |
| Create short URLs | **No** (Forbidden) | Yes | Yes |
| View platform-wide URLs | Yes | No | No |
| View company URLs | Yes | Yes (own company) | No |
| View personal URLs | N/A | Yes | Yes |
| Export CSV report | Platform-wide | Own company | Own links |
| Public redirect (`/r/{code}`) | Public | Public | Public |

---

## Implementation Details

### Multi-Tenancy & Authorization
- Every short URL and invitation is tied to a `company_id`.
- Access is controlled via Laravel Policies (`ShortUrlPolicy`) and Gates (`manage-companies`, `manage-team`).
- Super Admins are restricted at the policy, request, and controller layers from creating short URLs.
- Members can only query or view URLs where `user_id == auth()->id()`.

### Invitation Workflow
- Tokens are 64 random characters. The database stores only the SHA-256 hash (`token_hash`) alongside an expiration timestamp (`now()->addDays(7)`).
- When the invitee visits `/invitations/{token}`, the token is hashed and looked up. Upon setting their name and password, their user account is created with the designated role and company, and the invitation record is marked accepted.

### URL Shortening & Collision Handling
- `ShortUrlService` creates unique alphanumeric codes.
- If a random collision ever occurs, the service catches the constraint and generates a new code before persisting.
- Redirections (`/r/{code}`) perform an atomic increment on the `clicks` counter and return a 302 redirect.

---

## Running Tests

The test suite covers role authorization, tenant isolation, invitation acceptance, and redirection logic.

Run all tests:
```bash
php artisan test
```

Run authorization tests specifically:
```bash
php artisan test --filter=ShortUrlAuthorizationTest
```

### What the tests cover:
- Super Admin blocked from creating short URLs (403 Forbidden).
- Super Admin access to all company records and platform exports.
- Company Admin restricted to their own company's data and team invites.
- Member restricted to their own links only.
- Invitation token lifecycle (valid token, expired token, user creation).
- Public short URL redirect and click incrementing.
- Dashboard date filters and CSV export streaming.

---

## Key Files

- `app/Services/ShortUrlService.php` - Short code generator and collision handling.
- `app/Policies/ShortUrlPolicy.php` - Role authorization rules for URL creation and viewing.
- `app/Http/Controllers/DashboardController.php` - Dashboard metrics, date filtering, and role scoping.
- `app/Http/Controllers/InvitationController.php` - Invitation token verification and acceptance.
- `app/Http/Controllers/RedirectController.php` - Public redirection and click tracking.
- `database/seeders/SuperAdminSeeder.php` - Raw SQL parameterized insert for Super Admin.
- `tests/Feature/ShortUrlAuthorizationTest.php` - Feature tests for roles, permissions, and redirects.

---

## AI Usage Disclosure

In line with the assignment guidelines, here is a note on AI tool usage:

- **ChatGPT**: Used to assist with UI component generation (Tailwind CSS layouts and modal interactions) as well as quick syntax reference for specific edge cases.
- **Original Work**: The application architecture, multi-tenant database schema, policy and gate authorization logic, invitation token lifecycle, raw SQL seeder, and automated test suite were designed, written, and verified by me.