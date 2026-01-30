
# Task App (Symfony 8) – Technical Assignment

A simple task management application built with Symfony 8 and Doctrine ORM.

---

## Features


## Backend functionality

- CRUD operations for Teams, Projects and Tasks
- Project details page with tasks grouped by status and progress bar
- Team dashboard (Home):
    - active projects count
    - task statistics grouped by status
    - scope selector (All teams / specific team)

---

## Tech stack

- PHP 8.4+
- Symfony 8.0
- Doctrine ORM + Migrations
- Twig + Bootstrap 5
- PHPUnit
- PHPStan
- PHP_CodeSniffer
- Faker + DoctrineFixturesBundle
- CaptainHook (git hooks)
- Docker & Docker Compose (database)

---

## Requirements

- PHP 8.4+
- Composer
- Docker & Docker Compose
- SQLite extension (`pdo_sqlite`) for running tests

---

## Installation & Setup

### 1) Install PHP dependencies
```bash
composer install
```

### 2) Start database using Docker Compose
The application uses **MySQL 8** via Docker Compose for local development.

```bash
docker compose up -d
```

Make sure the database container is running before continuing.

### 3) Environment configuration
Create a local environment file:
```bash
cp .env .env.local
```

Example database configuration:
```env
DATABASE_URL="mysql://task_app:task_app@127.0.0.1:3306/task_app?serverVersion=8.0"
```

### 4) Database setup
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5) Load demo data (optional)
```bash
php bin/console doctrine:fixtures:load
```

### 6) Run the application
```bash
symfony serve
```

Open in browser:
```
http://127.0.0.1:8000
```

---


```bash
composer test
```

The `composer test` command performs:
1. Drop test database (if exists)
2. Create test database
3. Run Doctrine migrations in test environment
4. Execute PHPUnit test suite

---

## Git Hooks

Git hooks are configured using **CaptainHook**.

### Pre-commit
Runs:
- PHP lint
- PHPCS (staged PHP files only)
- PHPStan (staged PHP files only)

### Pre-push
Runs:
- PHPStan (if PHP files changed)
- PHPUnit (if PHP files changed)

Configuration file:
```
captainhook.json
```

---

## Project structure (high-level)

```
src/
├── Controller/     # HTTP layer
├── Entity/         # Doctrine entities
├── Repository/     # Query logic and aggregates
├── Service/        # Application / domain services
├── Manager/        # Persistence helpers
├── DataFixtures/   # Faker-based fixtures
templates/          # Twig templates and UI macros
tests/              # Functional tests
```

---


## Useful commands

```bash
docker compose up -d
php bin/console cache:clear
php bin/console debug:router
php bin/console doctrine:schema:validate
php bin/console doctrine:fixtures:load
composer test
```
