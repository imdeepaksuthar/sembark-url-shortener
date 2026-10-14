# Sembark URL Shortener

A URL shortener built with Laravel 12, MySQL, Tailwind CSS, and Alpine.js for the Sembark technical assessment.

It supports multi-company management, role-based access control (Super Admin, Company Admin, Member), token-based email invitations, link analytics with CSV export, and public `/r/{code}` redirection.

---

## Setup & Installation

### 1. Clone & install dependencies
```bash
git clone https://github.com/imdeepaksuthar/sembark-url-shortener.git
cd sembark-url-shortener

composer install
npm install
```

### 2. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```
Set your database credentials in `.env`:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sembark_url_shortener
DB_USERNAME=root
DB_PASSWORD=

# Keep mail driver as log for local testing (sent to storage/logs/laravel.log)
MAIL_MAILER=log
```

### 3. Run migrations and seed data
```bash
php artisan migrate --seed
```
*Note: The Super Admin user is created via parameterized raw SQL (`DB::insert`) inside `SuperAdminSeeder` per the assignment requirements.*

### 4. Start the application
```bash
npm run build     # or: npm run dev
php artisan serve
```
Visit **http://localhost:8000**.

---

## Default Login & Testing Flow

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `superadmin@example.com` | `password` |

### Testing the invite flow:
1. Log in as Super Admin.
2. Click **"+ Invite Client"** to create a company and send an invitation.
3. Check `storage/logs/laravel.log` for the generated invite link (`http://localhost:8000/invitations/<token>`).
4. Open the link to set a password and activate the Company Admin account.
5. Log in as the Company Admin to create short links, view company analytics, or invite team members.

---

## Roles & Permissions

| Action | Super Admin | Company Admin | Member |
|---|:---:|:---:|:---:|
| Create companies & invite Company Admins | Yes | No | No |
| Invite team admins & members | No | Yes (own company) | No |
| Create short URLs | No | Yes | Yes |
| View platform-wide URLs | Yes | No | No |
| View company URLs | Yes | Yes (own company) | No |
| View personal URLs | N/A | Yes | Yes |
| Export CSV report | Platform-wide | Own company | Own links |
| Public redirect (`/r/{code}`) | Public | Public | Public |

---

## Running Tests

Run the test suite to verify role boundaries, invitations, and redirects:

```bash
php artisan test
```

Or run the authorization tests specifically:
```bash
php artisan test --filter=ShortUrlAuthorizationTest
```

---

## AI Usage Disclosure

In line with the assignment guidelines, here is a note on AI tool usage:

- **ChatGPT**: Used to assist with UI component generation (Tailwind CSS layouts and modal interactions) as well as quick syntax reference for specific edge cases.
- **Original Work**: The application architecture, company-scoped database schema, policy and gate authorization logic, invitation token lifecycle, raw SQL seeder, and automated test suite were designed, written, and verified by me.