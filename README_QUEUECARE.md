# QueueCare — Serial & Queue Management System (Laravel 11)

Clinic flow: Registration → Service/Doctor → Auto Counter → Serial (G-001) → Print → Waiting → Operator Next/Start/Complete/Skip/Recall/Cancel → Live Display → Reports.

## Setup
1. `composer install`
2. Database is MySQL (XAMPP): create `queuecare` + `queuecare_testing` (utf8mb4), then set `.env` to `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=queuecare`, `DB_USERNAME=root`, `DB_PASSWORD=` (same values already in `.env` / `.env.example`).
3. `php artisan key:generate`
4. `php artisan migrate:fresh --seed`
5. Frontend: built assets ship in `public/build`. If you edit CSS/JS, rebuild with `"./node_modules/vite/bin/vite.js" build` (Git Bash) — plain `npm run build` fails because the folder path contains spaces/`&`.
6. `php artisan serve` → http://127.0.0.1:8000

## Logins (seeded, password: `password123`)
- Super Admin: `superadmin@queuecare.local` — everything incl. Roles & Access
- Admin: `admin@queuecare.local` — users (non-super), masters, reports, settings
- Receptionist: `reception@queuecare.local` — new tokens, history, waiting view
- Operator: `operator@queuecare.local` — My Queue (Counter-1)
- Staff: `staff@queuecare.local` — assigned service workspace
- Display: `display@queuecare.local` — TV display setup

Permissions live in `config/rbac.php` (single matrix, Super Admin `*` bypass).

## Key rules
- Daily serials per service: `PREFIX-001…`, atomic via `lockForUpdate`, unique `(date, service, seq)` + `(date, token_no)`. Queries use `whereDate` (SQLite/MySQL safe).
- Queue: one active (`calling`/`serving`) per counter; Next takes oldest `waiting` of the counter's service. Invalid transitions return friendly errors.
- Display: public `/display`, polls `/api/display` every 4s.
- Tests: `php artisan test` runs against the `queuecare_testing` MySQL database (`phpunit.xml`), dev DB untouched.
