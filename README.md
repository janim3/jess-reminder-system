# Jess Reminder System

A fully functional automated reminder system built with Laravel. Stores contacts and message templates, assigns templates to contacts with configurable schedules, and sends automated SMS (mock) and email reminders via cron jobs.

---

## Features

- **Contact Management** — CRUD with phone, email, and date of birth
- **Message Templates** — CRUD with `{name}` variable support
- **Assignments** — Link contacts to templates with frequency, send times, and channel (SMS / Email / Both)
- **Automated Scheduling** — Laravel Scheduler fires every minute and dispatches jobs for due assignments
- **Queue System** — All sends are queued jobs with retry logic
- **Mock SMS Service** — Swappable SMS interface with a log-based mock implementation
- **REST API** — Full API for contacts, templates, and assignments
- **Bootstrap UI** — Clean Blade views with Bootstrap 5

---

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+ (or MariaDB)
- A queue driver (database driver is included; Redis optional)

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/janim3/jess-reminder-system.git
cd jess-reminder-system

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 4. Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jess_reminder
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Configure mail in .env (example: Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="reminders@example.com"
MAIL_FROM_NAME="Jess Reminder System"
```

---

## Migration Commands

```bash
# Run all migrations
php artisan migrate

# (Optional) Run migrations fresh
php artisan migrate:fresh
```

---

## Queue Setup

The system uses the `database` queue driver by default.

```bash
# Ensure QUEUE_CONNECTION=database in .env

# Run the queue worker
php artisan queue:work --tries=3 --backoff=60
```

For production, use a process manager like Supervisor to keep the worker running.

---

## Cron Setup (IMPORTANT)

Add a single cron entry to your server to run the Laravel scheduler every minute:

```cron
* * * * * cd /path/to/jess-reminder-system && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler automatically runs `reminders:send` every minute, checking all assignments and dispatching queue jobs for any whose `send_times` match the current `HH:MM`.

**On Windows (development)**, use the built-in scheduler loop instead:
```bash
php artisan schedule:work
```

---

## Running Locally

```bash
# Terminal 1 — web server
php artisan serve

# Terminal 2 — queue worker
php artisan queue:work

# Terminal 3 — scheduler (development only)
php artisan schedule:work
```

Visit http://localhost:8000

---

## REST API

All endpoints are prefixed with `/api/`:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/contacts` | List contacts |
| POST | `/api/contacts` | Create contact |
| GET | `/api/contacts/{id}` | Get contact |
| PUT | `/api/contacts/{id}` | Update contact |
| DELETE | `/api/contacts/{id}` | Delete contact |
| GET | `/api/templates` | List templates |
| POST | `/api/templates` | Create template |
| GET | `/api/templates/{id}` | Get template |
| PUT | `/api/templates/{id}` | Update template |
| DELETE | `/api/templates/{id}` | Delete template |
| GET | `/api/assignments` | List assignments |
| POST | `/api/assignments` | Create assignment |
| GET | `/api/assignments/{id}` | Get assignment |
| PUT | `/api/assignments/{id}` | Update assignment |
| DELETE | `/api/assignments/{id}` | Delete assignment |

---

## Running Tests

```bash
php artisan test
```

Tests use SQLite in-memory — no database configuration needed.

---

## SMS Service

The SMS service is a mock that logs to `storage/logs/laravel.log`. To swap in a real provider, implement `App\Services\SmsServiceInterface` and bind it in `AppServiceProvider`:

```php
$this->app->bind(SmsServiceInterface::class, YourRealSmsService::class);
```
