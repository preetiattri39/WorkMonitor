# WorkPulse

WorkPulse is a modern Employee Monitoring and Productivity Management System built to help businesses manage teams, track employee performance, monitor productivity, and improve workflow efficiency through a centralized SaaS-style platform.

## Features

* Employee attendance tracking
* Daily work logs
* Task and project management
* Productivity monitoring
* Screenshot and activity tracking
* Real-time notifications
* Admin, Manager, and Employee roles
* Performance analytics and reports
* Leave management
* Audit logs
* Secure file uploads
* Responsive dashboard UI
* REST APIs
* Real-time updates with WebSockets/Reverb

## Tech Stack

* Laravel 12
* PHP 8.2
* MySQL
* Tailwind CSS
* Vue.js / Livewire
* Laravel Sanctum
* Laravel Reverb
* REST APIs

## Modules

### Employee Panel

* Check-in / Check-out
* Daily reports
* Task updates
* Notifications
* Activity tracking

### Admin Dashboard

* Employee management
* Productivity analytics
* Attendance reports
* Team performance charts
* Project tracking
* Leave approvals
* Activity monitoring

## Installation

```bash
git clone https://github.com/yourusername/workpulse.git
cd workpulse
composer install
cp .env.example .env
php artisan key:generate
```

## Configure Database

Update `.env` file:

```env
DB_DATABASE=workpulse
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

Install frontend dependencies:

```bash
npm install
npm run dev
```

Start server:

```bash
php artisan serve
```

## Future Enhancements

* AI productivity insights
* Payroll integration
* GPS tracking
* Desktop monitoring agent
* Advanced reporting
* Mobile application

## Author

Preeti Attri

Private Project – All Rights Reserved.
