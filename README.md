# University Management System

A school management web app built with **Core PHP 8+**: authentication (login/logout) plus CRUD for students, teachers, class schedules, and discipline records. It uses a small custom MVC stack (router, query builder/ORM, validator, sessions) and Bootstrap 5 for the UI. No framework is required.

## Features

- Session-based authentication with password hashing (`password_hash` / `password_verify`)
- Login, logout, and auth-protected routes
- Students: search, status filter, pagination, full CRUD, discipline history
- Teachers: search, status filter, pagination, full CRUD, assigned schedule
- Class schedule: weekly timetable with day/subject filters, CRUD, teacher assignment
- Discipline records: filters, CRUD, severity/status badges, resolution timestamps
- CSRF protection on all state-changing requests
- Server-side validation with old-input repopulation and inline errors
- Custom ORM with prepared statements (PDO)
- SQLite by default with automatic schema creation and demo seed data
- Optional MySQL support via `database.sql`

## Requirements

- PHP 8.0+ with `pdo_sqlite` (default) or `pdo_mysql`
- No Composer packages are required; a fallback autoloader runs when `vendor/autoload.php` is absent
- PHP built-in server, Apache, or Nginx

## Quick Start

```bash
php -S localhost:8000 -t public
```

Open <http://localhost:8000>. On first database access the app creates `storage/database.sqlite` and seeds demo users, teachers, students, schedules, and discipline records.

### Demo Accounts

| Role  | Email                    | Password   |
|-------|--------------------------|------------|
| Admin | `admin@university.edu`   | `password` |
| Teacher | `teacher@university.edu` | `password` |

## Configuration

Settings are read from `$_ENV` (an optional `.env` file in the project root is loaded automatically).

```ini
APP_DEBUG=true

# Default: SQLite (auto-migrates and seeds)
DB_DRIVER=sqlite
DB_DATABASE=storage/database.sqlite

# Optional: MySQL
# DB_DRIVER=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_NAME=university_crud
# DB_USER=root
# DB_PASS=
```

For MySQL, import `database.sql` first. Auto-migration is disabled for MySQL.

## Project Structure

```
app/
  config/       DatabaseConfig
  controllers/  Auth, Home, Student, Teacher, Schedule, Discipline
  core/         Router, ORM, QueryBuilder, Validator, Auth, Session,
                Database, Migrator, BaseController, BaseModel, exceptions
  Models/       User, Student, Teacher, Schedule, Discipline
public/
  index.php     Front controller, autoload, .env loader, routes, CSRF
storage/
  database.sqlite
views/
  auth/         login
  home/         dashboard
  students/     index, create, edit, show
  teachers/     index, create, edit, show
  schedules/    index, create, edit
  disciplines/  index, create, edit, show
  layouts/      app, auth
  partials/     pagination
database.sql    MySQL schema + demo accounts
```

## Routes

| Method | URI | Action |
|--------|-----|--------|
| GET | `/login` | Login form |
| POST | `/login` | Authenticate |
| POST | `/logout` | Sign out |
| GET | `/` | Dashboard |
| GET/POST | `/students` | List / create |
| GET | `/students/create` | Create form |
| GET/POST | `/students/{id}` | Show / update / delete (`_method`) |
| GET | `/students/{id}/edit` | Edit form |
| GET/POST | `/teachers` | List / create |
| GET | `/teachers/create` | Create form |
| GET/POST | `/teachers/{id}` | Show / update / delete (`_method`) |
| GET | `/teachers/{id}/edit` | Edit form |
| GET/POST | `/schedules` | List / create |
| GET | `/schedules/create` | Create form |
| GET/POST | `/schedules/{id}` | Update / delete (`_method`) |
| GET | `/schedules/{id}/edit` | Edit form |
| GET/POST | `/disciplines` | List / create |
| GET | `/disciplines/create` | Create form |
| GET/POST | `/disciplines/{id}` | Show / update / delete (`_method`) |
| GET | `/disciplines/{id}/edit` | Edit form |

HTML forms that update or delete send `_method=PUT|DELETE` together with the CSRF `_token`.

## Notes

- Delete a teacher: assigned schedules keep their slot and become unassigned (`ON DELETE SET NULL`).
- Delete a student: related discipline records are removed (`ON DELETE CASCADE`).
- Passing `?student_id={id}` to `/disciplines/create` preselects that student.
- The app re-seeds only empty tables; deleting the SQLite file resets to demo data.
