# Expense Tracker
this is a simple expense tracker system made with laravel, using laravel balde as template view, tailwind css for styling, and datatables for serving data.

## Prerequisite
- PHP 8.3
- PostgreSQL

## Installation
- Clone this project using command `git clone git@github.com:fiqrikm18/ExpenseTracker.git`
- Go to inside project directory.
- Copy `.env.example` to `.env`.
- Change configuration for database on section, for example:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ExpenseTracker
DB_USERNAME=postgres
DB_PASSWORD=postgres
```
- Run command `composer install` to install dependencies needed by app.
- Run `npm run install` or `yarn install` for install dependencies for view.
- Run command `php artisan migrate` and `php artisan migrate --seed` to run database migration and seed.
- Run `php artisan serve` to run application or if you are using laravel herd or valet just open your local domain on browser.
