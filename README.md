# Sembark URL Shortener

A robust, multi-tenant URL shortening and link analytics platform built with **Laravel 12**, **MySQL**, **Tailwind CSS**, and **Alpine.js**.

This project implements multi-tenant company isolation, a strict 3-tier Role-Based Access Control (RBAC) hierarchy, token-based invitation workflows, role-scoped short URL management, real-time click tracking, and an automated test suite.

---

## Table of Contents

- [Features Overview](#features-overview)
- [Requirements & Tech Stack](#requirements--tech-stack)
- [Step-by-Step Installation Guide](#step-by-step-installation-guide)
- [Default Seeded Credentials](#default-seeded-credentials)
- [Role & Authorization Matrix](#role--authorization-matrix)
- [Core Workflows](#core-workflows)
  - [1. User & Team Invitations](#1-user--team-invitations)
  - [2. URL Shortening & Collision Handling](#2-url-shortening--collision-handling)
  - [3. Public Redirection & Click Analytics](#3-public-redirection--click-analytics)
- [Running Automated Tests](#running-automated-tests)
- [Project Architecture & Key Files](#project-architecture--key-files)
- [Acceptable AI Usage Policy Declaration](#acceptable-ai-usage-policy-declaration)

---

## Features Overview

- **Multi-Company Architecture**: Complete data isolation across client companies.
- **3-Tier Role Hierarchy**:
  - `Super Admin`: Platform oversight; manages companies, invites company admins, monitors platform-wide analytics; **cannot** create short URLs.
  - `Admin`: Company administrator; creates short URLs, invites team admins/members to own company, monitors company-wide links and analytics.
  - `Member`: Team member; creates short URLs, views only their own links and personal click analytics.
- **Database Seeder via Raw SQL**: Seeds the initial Super Admin account using parameterized raw SQL queries (`DB::insert(...)`) as specified in the assignment requirements.
- **Secure Invitation System**: Cryptographically hashed tokens (SHA-256) with 7-day expiration for inviting company admins and team members via email.
- **High-Performance Public Redirection**: Fast lookup (`/r/{code}`) with atomic click counter incrementation and safe redirection.
- **Interactive Dashboard UI**:
  - Filter analytics by time period (`Today`, `Last Week`, `This Month`, `Last Month`, `All Time`).
  - Streaming CSV export for short link analytics.
  - Paginated data tables with initial 2-record view and interactive `[ View All ]` toggles.
  - Responsive modals for link creation and team invitations with automated alert dismissal.

---

## Requirements & Tech Stack

Ensure your local development environment meets the following requirements:

| Component | Minimum Version | Recommended Version |
| :--- | :--- | :--- |
| **PHP** | `^8.2` | `8.2` or `8.3` |
| **Composer** | `2.x` | Latest `2.x` |
| **Database** | MySQL `8.0+` or MariaDB `10.4+` (SQLite supported) | MySQL `8.0+` |
| **Node.js & npm** | Node `18.x+`, npm `9.x+` | Node `20.x LTS` |
| **Web Server** | Built-in PHP server, Apache, or Nginx | Laragon / PHP CLI Server |

### Required PHP Extensions
- `BCMath`
- `Ctype`
- `Fileinfo`
- `JSON`
- `Mbstring`
- `OpenSSL`
- `PDO` & `pdo_mysql` (or `pdo_sqlite`)
- `Tokenizer`
- `XML`

---

## Step-by-Step Installation Guide

Follow these steps to set up and run the project locally from scratch:

### 1. Clone the Repository

```bash
git clone https://github.com/imdeepaksuthar/sembark-url-shortener.git
cd sembark-url-shortener
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Frontend Dependencies

```bash
npm install
```

### 4. Configure Environment Variables

Duplicate the `.env.example` file to create your active `.env` configuration:

**On Linux / macOS:**
```bash
cp .env.example .env
```

**On Windows (PowerShell / Command Prompt):**
```powershell
copy .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Configure the Database

Open the `.env` file in your editor and configure your database connection:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sembark_url_shortener
DB_USERNAME=root
DB_PASSWORD=
```

> **Note for MySQL users**: Make sure the database exists in your MySQL server before running migrations:
> ```sql
> CREATE DATABASE sembark_url_shortener CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> ```
> *(If using SQLite, set `DB_CONNECTION=sqlite` and create an empty `database/database.sqlite` file).*

### 7. Configure Super Admin Credentials & Mail

Verify the Super Admin credentials and application details in your `.env`:

```dotenv
APP_NAME="Sembark URL Shortener"
APP_URL=http://localhost:8000

SUPER_ADMIN_EMAIL=superadmin@example.com
SUPER_ADMIN_PASSWORD="password"

# For local email testing (emails will be logged to storage/logs/laravel.log):
MAIL_MAILER=log
```

### 8. Run Database Migrations and Seeders

Run the database migrations and seed the initial Super Admin account:

```bash
php artisan migrate --seed
```

> **Note**: The `SuperAdminSeeder` runs parameterized raw SQL to insert the primary Super Admin account into the database (`DB::insert(...)`), fulfilling the assignment specification.

### 9. Build Frontend Assets

You can either compile the assets for production:

```bash
npm run build
```

Or run the Vite development server with hot-module replacement:

```bash
npm run dev
```

### 10. Start the Local Development Server

Run Laravel's built-in server:

```bash
php artisan serve
```

The application is now live at: **[http://localhost:8000](http://localhost:8000)** (or `http://127.0.0.1:8000`).

---

## Default Seeded Credentials

When running `php artisan migrate --seed`, the following default Super Admin account is provisioned:

| Role | Email | Password | Scope / Permissions |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@example.com` | `password` | Global platform access, company invitations, system-wide analytics (cannot generate short URLs). |

To test company-level features, log in as `superadmin@example.com`, navigate to the dashboard, and use **"+ Invite Client"** to invite a Client Admin to a new or existing company.

---

## Role & Authorization Matrix

The platform enforces strict role boundaries implemented via Laravel Policies (`ShortUrlPolicy`, `InvitationPolicy`, `CompanyPolicy`) and Form Requests:

| Feature / Action | Super Admin | Company Admin | Team Member |
| :--- | :---: | :---: | :---: |
| **Log in & Log out** | Yes | Yes | Yes |
| **Create New Company** | Yes | No | No |
| **Invite Company Admin** | Yes | No | No |
| **Invite Team Admin / Member** | No | Yes (own company) | No |
| **Create Short URLs** | **No** (Forbidden) | **Yes** | **Yes** |
| **View All Platform URLs** | **Yes** (Across all companies) | No | No |
| **View Company URLs** | Yes | **Yes** (Own company only) | No |
| **View Personal URLs** | N/A | Yes | **Yes** (Own only) |
| **Public URL Redirection (`/r/{code}`)** | Public | Public | Public |
| **Real-time Click Tracking** | Yes | Yes | Yes |
| **CSV Export of Short URLs** | Yes (All companies) | Yes (Own company) | Yes (Own URLs) |

---

## Core Workflows

### 1. User & Team Invitations
- **Super Admin Flow**:
  - Super Admin opens the dashboard and clicks **"+ Invite Client"**.
  - Can choose **Create New Company** (specifying company name and Admin email) or **Existing Company** (selecting a registered company).
  - An invitation token is generated with a 7-day expiration (`expires_at = now()->addDays(7)`) and stored as a secure SHA-256 hash.
  - The invited admin receives an email with an acceptance link (`/invitations/{token}`).
- **Company Admin Flow**:
  - Company Admin clicks **"+ Invite Member"** on their dashboard.
  - Can invite new colleagues with either `Admin` or `Member` role within their own company.
  - Invitee receives an email to register their account and joins the company workspace.

### 2. URL Shortening & Collision Handling
- **Generation**:
  - `ShortUrlService` creates unique Base62/alphanumeric codes.
  - Automatically handles duplicate or collision retries with database-level uniqueness constraints.
- **Constraints**:
  - Super Admin is explicitly blocked at policy, request, and controller levels from creating short URLs.
  - Short URLs are tagged with both `company_id` and `user_id` to ensure strict tenant isolation.

### 3. Public Redirection & Click Analytics
- Anyone accessing `http://localhost:8000/r/{code}`:
  - If code is valid, the application atomically increments the `clicks` counter (`clicks = clicks + 1`).
  - Redirects with an HTTP `302 Found` to the destination URL.
  - If the code is invalid or does not exist, an HTTP `404 Not Found` response is returned.

---

## Running Automated Tests

A comprehensive suite of feature tests covers all authorization rules, role restrictions, public redirection, and invitation lifecycles.

To execute the test suite, run:

```bash
php artisan test
```

To run only the authorization and business logic test suite:

```bash
php artisan test --filter=ShortUrlAuthorizationTest
```

### Verified Test Cases
- `test_member_can_create_a_short_url`: Validates that members can create short URLs.
- `test_super_admin_cannot_create_a_short_url`: Ensures Super Admin creation attempts return HTTP 403 Forbidden.
- `test_super_admin_has_access_to_whole_system_data`: Confirms Super Admin can view all companies, users, and short URLs.
- `test_admin_has_access_to_all_data_of_own_company_and_can_invite_admin_and_member`: Verifies company admin isolation and team invitation capabilities.
- `test_member_has_only_access_to_data_created_by_itself`: Verifies members only see their personal URLs and cannot see other members' links.
- `test_short_url_redirects_to_original_url_and_increments_clicks`: Validates public redirection and atomic click tracking.
- `test_short_url_redirect_returns_404_for_invalid_code`: Verifies 404 responses for missing codes.
- `test_guest_can_view_invitation_form_and_accept`: Tests the complete invitation acceptance lifecycle.
- `test_dashboard_ajax_filter_returns_json_html_and_export_url`: Validates date-filtering and dynamic table rendering.

---

## Project Architecture & Key Files

```text
sembark-url-shortener/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php      # Role-specific dashboard metrics, pagination & filtering
│   │   │   ├── ShortUrlController.php       # URL management, creation, CSV export & pagination
│   │   │   ├── InvitationController.php     # Token verification & invitation acceptance
│   │   │   └── RedirectController.php       # Public /r/{code} redirect handler & click tracking
│   │   └── Requests/
│   │       ├── StoreShortUrlRequest.php     # Role validation & URL sanitization
│   │       └── InviteCompanyAdminRequest.php# Super Admin invitation request validation
│   ├── Mail/
│   │   └── UserInvitationMail.php           # Mailable for sending invitation emails
│   ├── Models/
│   │   ├── Company.php                      # Company tenant model
│   │   ├── User.php                         # User model with role constants (SUPER_ADMIN, ADMIN, MEMBER)
│   │   ├── ShortUrl.php                     # Short URL model with click counters
│   │   └── Invitation.php                   # Token-based invitation records
│   ├── Policies/
│   │   └── ShortUrlPolicy.php               # Granular authorization gates per role
│   └── Services/
│       └── ShortUrlService.php              # Short code generator & collision resolution
├── config/
│   ├── app.php                              # Application name and locale settings
│   └── superadmin.php                       # Super Admin configuration bindings
├── database/
│   ├── migrations/                          # Database schema migrations
│   └── seeders/
│       ├── DatabaseSeeder.php               # Root seeder
│       └── SuperAdminSeeder.php             # Raw SQL Super Admin seeder
├── resources/
│   └── views/
│       ├── dashboard.blade.php              # Multi-tenant dashboard with modals & metrics
│       ├── dashboard/partials/
│       │   └── urls-table.blade.php         # Short URLs table with pagination & date filters
│       ├── emails/
│       │   └── invitation.blade.php         # Clean, human-written invitation email template
│       ├── invitations/
│       │   └── accept.blade.php             # Invitation acceptance and password set form
│       └── short-urls/
│           └── index.blade.php              # Standalone short URLs view
└── tests/
    └── Feature/
        └── ShortUrlAuthorizationTest.php    # Complete authorization and feature test suite
```

---

## Acceptable AI Usage Policy Declaration

In adherence to the **Acceptable AI Usage Policy** outlined in the assignment instructions:

1. **Tools Utilized**:
   - **Gemini / Antigravity Assistant**: Used for syntax lookup, refining Tailwind CSS responsive utilities, formulating Alpine.js modal transitions, structuring clean Markdown documentation, and identifying potential edge cases for PHPUnit test assertions.
2. **Original Reasoning & Architecture**:
   - All core architectural decisions—including the multi-tenant database schema, policy-based authorization logic, role isolation rules, SHA-256 tokenized invitation lifecycle, parameterized raw SQL seeder, and Base62 collision resolution—were uniquely reasoned, designed, and implemented specifically to satisfy the project's functional specifications.

---

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).