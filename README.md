# MonitorFlow

MonitorFlow is a Laravel 12 based Employee Monitoring and Productivity Management System scaffold for SaaS-style operations teams. It combines attendance, daily work logs, tasks, leave workflows, activity tracking, screenshot uploads, real-time notifications, and management analytics in a single platform.

## Stack

- Laravel 12
- MySQL
- Tailwind CSS
- Vue 3
- Livewire 3
- Laravel Reverb
- Sanctum

## Included modules

- Secure authentication foundation using Laravel verified users, Sanctum-protected APIs, and role-aware route middleware
- Role-based access via `admin`, `manager`, and `employee`
- Attendance tracking with status normalization
- Daily work logs tied to tasks and projects
- Project and task management
- Productivity activity ingestion and screenshot monitoring APIs
- Leave management workflow
- Audit logging service
- Realtime task and productivity events over Reverb
- Vue analytics dashboard with charts
- Livewire presence widget

## Architecture

- `app/Models`: core domain entities for HR, monitoring, productivity, and audit records
- `app/Http/Controllers`: web and API controllers split by interaction surface
- `app/Services`: analytics, scoring, and audit concerns
- `app/Events` and `app/Notifications`: realtime and async user updates
- `resources/js`: Vue dashboard shell and chart components
- `resources/views`: landing page, app entrypoint, and lightweight blade sections
- `database/migrations`: schema for monitoring and productivity tables

## Setup

1. Install PHP 8.2+, Composer, Node.js 20+, and MySQL.
2. Run `composer install`.
3. Run `npm install`.
4. Add your preferred Laravel auth layer if you want complete registration and login screens immediately:
   use the official Laravel 12 Vue or Livewire starter kit, or wire Fortify manually.
5. Copy `.env.example` to `.env`, then update database, mail, queue, and Reverb settings.
6. Generate the app key with `php artisan key:generate`.
7. Run migrations and seeders with `php artisan migrate --seed`.
8. Create storage symlink with `php artisan storage:link`.
9. Start the stack with `composer run dev`.

## Production notes

- Put queues behind a worker such as Horizon or Supervisor.
- Use S3-compatible object storage for screenshots in production.
- Move screenshot classification and daily productivity aggregation to queued jobs.
- Add an endpoint signing strategy or device token flow for desktop capture agents.
- Expand policy coverage and install the final auth UI flow before exposing publicly.

## Verification status

This repository was scaffolded without the ability to run Composer, NPM, Artisan, or tests in-session because the local exec tool was unavailable. The structure and code are organized to fit a standard Laravel 12 application, but package installation, migration execution, and runtime verification still need to be performed locally.
