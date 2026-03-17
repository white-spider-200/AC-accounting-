# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 9 application with Vite-managed frontend assets. Core backend code lives in `app/`, route definitions in `routes/`, Blade templates in `resources/views/`, and frontend entry points in `resources/js/` and `resources/sass/`. Database migrations, factories, and seeders are under `database/`. Publicly served files, uploaded assets, and vendor UI packages are in `public/`. Tests are split into `tests/Feature/` and `tests/Unit/`.

## Build, Test, and Development Commands
- `php artisan serve`: run the local Laravel server.
- `npm run dev`: start the Vite dev server for `resources/js/app.js` and `resources/sass/app.scss`.
- `npm run build`: create a production asset build.
- `php artisan migrate`: apply database migrations.
- `php artisan test`: run the PHPUnit suite through Laravel.
- `./vendor/bin/phpunit`: run PHPUnit directly.
- `./vendor/bin/pint`: format PHP code to the project standard.

Run `composer install` and `npm install` after cloning or when dependencies change.

## Coding Style & Naming Conventions
Follow `.editorconfig`: UTF-8, LF line endings, and 4-space indentation for source files; YAML uses 2 spaces. Keep PHP classes PSR-4 aligned with `App\\...` namespaces and use singular model names such as `Product` or `Warehouse`. Blade views should use descriptive lowercase names in feature folders, for example `resources/views/admin/products/create.blade.php`. Prefer formatting PHP with Laravel Pint before opening a PR.

## Testing Guidelines
Place request and integration coverage in `tests/Feature/*Test.php`; keep isolated logic in `tests/Unit/*Test.php`. PHPUnit is configured to include `app/` in coverage, so new business logic should be exercised there. Use descriptive test names ending in `Test.php`, and run `php artisan test` before submitting changes.

## Commit & Pull Request Guidelines
Git history is not available in this workspace snapshot, so no repository-specific commit convention could be verified. Use short imperative commit subjects, for example `Add warehouse validation`. PRs should explain the user-visible change, call out database or `.env` updates, link the related issue, and include screenshots for Blade/UI changes.

## Configuration & Data Notes
Start from `.env.example` when configuring a new environment. Keep secrets in `.env`, never in committed files. This project includes `database/database.sqlite`; if you use SQLite locally, confirm your test and app database settings before running migrations.
