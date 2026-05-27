# WorkMonitor

WorkMonitor is a Laravel 12 employee monitoring and productivity management platform built for SaaS-style operations teams. It centralizes attendance, daily work logs, project delivery, productivity tracking, screenshot monitoring, leave workflows, auditability, and realtime updates in one role-aware workspace.

## Stack

- Laravel 12
- PHP 8.2
- MySQL
- Tailwind CSS
- Vue 3
- Livewire 3
- Laravel Sanctum
- Laravel Reverb

## Core Capabilities

- Secure authentication foundation with verified users and role-aware access for `admin`, `manager`, and `employee`
- Attendance tracking with clock-in, clock-out, status normalization, and reports
- Daily work logs linked to tasks and projects
- Task and project management workflows
- Productivity monitoring with activity ingestion APIs
- Screenshot capture uploads with secure storage support
- Leave request submission and approval flows
- Audit logging for critical CRUD actions
- Realtime notifications and dashboard refresh via Reverb
- Responsive SaaS-style dashboard with charts and operational modules
- REST APIs for monitoring and task integrations

## Modules

### Employee Workspace

- Check-in and shift updates
- Daily work log submission
- Assigned task visibility and updates
- Leave request submission
- Productivity and activity visibility

### Manager and Admin Workspace

- Workforce attendance oversight
- Project and task delivery monitoring
- Leave approval queue
- Productivity analytics and performance board
- Screenshot and activity review
- Audit log visibility

## Architecture

- `app/Models`: HR, delivery, monitoring, productivity, and audit entities
- `app/Http/Controllers`: web and API controllers separated by interaction surface
- `app/Services`: analytics, productivity scoring, and audit concerns
- `app/Events` and `app/Notifications`: realtime and user-facing updates
- `resources/js`: Vue dashboard shell and chart components
- `resources/views`: Blade login, operational pages, and shared SaaS shell
- `database/migrations`: workforce, monitoring, and audit schema

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

## Seeded Access

- `admin@monitorflow.test` / `password`
- `manager@monitorflow.test` / `password`

## Notes

- Configure MySQL, mail, queue, and Reverb values in `.env` before running in a shared environment.
- For production, move screenshots to S3-compatible object storage and run queue workers under Supervisor or Horizon.
- Monitoring uploads and realtime features assume the Reverb and filesystem configuration is set correctly.

## Author

Preeti Attri
