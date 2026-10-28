# WashUp — Laundry Shop Management System

WashUp is a web-based Laundry Shop Management System developed for **Web Systems and Technologies 1**. It helps laundry staff manage customer orders, calculate laundry charges, maintain service and add-on prices, monitor order statuses, and review customer feedback. Customers can use the public Customer Order Tracker without creating an account.

> **Project status:** This README documents local setup and intended workflows. It does not mean the application has been deployed to a public hosting service.

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Users and Access](#2-users-and-access)
3. [Features](#3-features)
4. [Technology Stack](#4-technology-stack)
5. [Project Structure](#5-project-structure)
6. [Prerequisites](#6-prerequisites)
7. [Installation and Setup](#7-installation-and-setup)
8. [Running WashUp](#8-running-washup)
9. [How to Use the System](#9-how-to-use-the-system)
10. [Database and Migrations](#10-database-and-migrations)
11. [Testing and Build Verification](#11-testing-and-build-verification)
12. [Troubleshooting](#12-troubleshooting)
13. [Deployment Guide](#13-deployment-guide)
14. [Design and Submission Materials](#14-design-and-submission-materials)
15. [Repository](#15-repository)

## 1. Project Overview

**Project name:** WashUp  
**Project type:** Web-based Laundry Shop Management System  
**Course:** Web Systems and Technologies 1

WashUp is designed to make routine laundry-shop tasks easier by keeping order details, service prices, add-ons, order statuses, and customer feedback in one application. It includes a staff-facing management area and a public-facing Customer Order Tracker.

## 2. Users and Access

### Laundry staff / admin
Staff members sign in to access management features. They can create and manage orders, maintain service and add-on information, and review operational information.

### Customers
Customers do **not** need a staff account and do not log in. They use the Customer Order Tracker page to look up an order using the order number given by laundry staff. Customers may also submit feedback or complaints through the available customer-facing feature.

### Access and privacy notes
- The public tracker should not reveal customer phone numbers.
- Customer phone numbers may be stored for staff contact when needed, but should remain private.
- Do not publish real passwords, database passwords, API secrets, or access tokens in this repository.
- If the instructor needs to test staff login, provide a dedicated demo account through a private submission channel.

## 3. Features

- **Staff Login:** Authenticates authorized laundry staff.
- **Dashboard:** Provides an overview of laundry-shop activity.
- **Order Management:** Creates and manages customer laundry orders.
- **Order Statuses:** Uses the implemented simplified statuses: Pending, Ready for Pickup, and Claimed.
- **Price Calculation:** Calculates charges using the selected service, laundry weight, and applicable add-ons.
- **Service Management:** Stores laundry service options and prices.
- **Add-on Management:** Supports optional items such as fabric conditioner and extra drying.
- **Customer Order Tracker:** Lets customers search for their order by order number without logging in.
- **Feedback and Complaints:** Lets customers send feedback or complaints related to their order.
- **Reports:** Shows recorded laundry-operation information.
- **Receipt Popup:** Shows order details after an order is created. Physical printing depends on the implemented print functionality.

## 4. Technology Stack

| Component | Technology |
|---|---|
| Frontend | Vue 3 |
| Frontend development/build tool | Vite |
| Backend | Laravel |
| Backend language | PHP |
| Database | PostgreSQL |
| Frontend packages | npm |
| Backend packages | Composer |

## 5. Project Structure

The frontend and backend are kept together in one repository. The Customer Order Tracker is part of the same frontend application; it is not a separate project to install.

```text
WashUp/
├── backend/                 # Laravel backend
│   ├── app/                 # Models and application logic
│   ├── database/
│   │   ├── migrations/      # Database structure changes
│   │   └── seeders/         # Initial/sample records
│   ├── routes/              # Backend routes / API endpoints
│   ├── .env.example         # Example environment configuration
│   └── composer.json        # PHP dependencies
├── public/                  # Public frontend assets
├── src/                     # Vue pages and components, including tracker UI
├── tests/                   # Project test files, if configured
├── package.json             # Frontend scripts and dependencies
├── vite.config.js           # Vite configuration
└── README.md                # This guide
```

This is a simplified view of the main folders; some files may differ as the project evolves.

## 6. Prerequisites

Install the following on the computer that will run WashUp:
- Git
- Node.js and npm
- PHP version compatible with the Laravel project
- Composer
- PostgreSQL
- A code editor such as Visual Studio Code

The PHP installation must have the PostgreSQL PDO driver (`pdo_pgsql`) enabled. If multiple PHP installations are present on Windows, make sure the terminal uses the PHP installation with PostgreSQL support.

## 7. Installation and Setup

The following steps are for a local development setup on Windows. Adjust commands as needed for another operating system.

### Step 1 — Clone the repository

```bash
git clone https://github.com/xonikz-18/WashUp.git
cd WashUp
git checkout feature-backend
```

The `feature-backend` branch contains the project work referenced in this submission. If the instructor is using a merged or newer branch, use the branch specified by the project team.

### Step 2 — Create the PostgreSQL database

1. Start PostgreSQL.
2. Open pgAdmin or another PostgreSQL client.
3. Create a database named `washup_db`.
4. Keep note of the PostgreSQL username and password for your own local installation.

The database name, username, password, host, and port in the Laravel environment file must match the local PostgreSQL setup.

### Step 3 — Configure the Laravel backend

Open a terminal in the project root, then:

```bash
cd backend
composer install
```

Create the local environment file from the example if one does not already exist:

```powershell
Copy-Item .env.example .env
```

If `backend/.env` already exists, do not overwrite it; preserve the local settings and check that they are correct.

Open `backend/.env` and configure the database section. Example:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=washup_db
DB_USERNAME=postgres
DB_PASSWORD=your_local_postgres_password
```

Replace `your_local_postgres_password` with the password for the PostgreSQL account on that computer. Do not commit `backend/.env` to GitHub.

Generate the Laravel application key:

```bash
php artisan key:generate
```

Run the database migrations:

```bash
php artisan migrate
```

Migrations create the tables required by the application. If you are setting up a brand-new database, you may also run the seeder:

```bash
php artisan db:seed
```

**Important:** Back up any database with data before running database-changing commands. The seeder creates initial records as defined in `backend/database/seeders/DatabaseSeeder.php`; it does not necessarily reset an existing staff password. Verify the demo login separately.

Start the Laravel development server:

```bash
php artisan serve
```

Laravel usually runs at `http://127.0.0.1:8000`. Keep this terminal open while using the application.

**Windows PHP note:** If `php artisan` reports `could not find driver` but PostgreSQL support is installed in another PHP directory, run Artisan with the PHP executable that has `pdo_pgsql` enabled. For example, on the developer computer used for this project:

```powershell
C:\php\php.exe artisan migrate
C:\php\php.exe artisan serve
```

Use this path only if PHP is actually installed there on your computer.

### Step 4 — Install and run the Vue frontend

Open a **second terminal** at the project root (the `WashUp` folder, not `backend`):

```bash
npm install
npm run dev
```

Vite prints the local frontend URL in the terminal, commonly `http://localhost:5173`. Open the exact URL printed by your terminal.

Keep the frontend terminal open. The tracker is part of this frontend, so it uses the same frontend installation and development server.

### Step 5 — Configure the frontend-to-backend connection

The frontend must send API requests to the Laravel backend. Check the current frontend source and environment configuration for the API base URL or Vite environment variable used by the project. If an API URL needs to be configured, set it to the correct local Laravel URL and restart Vite.

Do not invent an environment variable name: use the one actually referenced by the frontend code. If requests fail, inspect the browser developer tools and Laravel logs for the actual endpoint and error.

## 8. Running WashUp

For local use, run both servers:

1. **Backend terminal:** inside `WashUp/backend`, run `php artisan serve` (or the correct PHP executable followed by `artisan serve`).
2. **Frontend terminal:** inside the root `WashUp` folder, run `npm run dev`.
3. Open the frontend URL printed by Vite.
4. Use the staff login for management features, or navigate to the Customer Order Tracker route configured in the Vue application.

The tracker is not a second application and does not need a separate `npm install` or separate backend installation. Its exact page URL depends on the route configured in the frontend. Use the route shown in the current application rather than guessing a path.

## 9. How to Use the System

### A. Laundry staff

1. Open the WashUp frontend.
2. Sign in using the authorized staff account.
3. Open the dashboard to view the available operational summary.
4. Open order management to create a customer order.
5. Enter the customer and laundry details required by the form.
6. Select a service, enter the laundry weight, and select any applicable add-ons.
7. Review the calculated total and create the order.
8. Review the receipt popup displayed after order creation.
9. Update the order to the appropriate supported status, such as Pending or Ready for Pickup; use Claimed when the customer has collected the order.
10. Use the service/add-on management and reports features as needed.
11. Review customer feedback and complaints through the available management feature.

Staff should check order details carefully before saving. Follow the application's current validation rules.

### B. Customers — Customer Order Tracker

1. Open the Customer Order Tracker page within the same WashUp website.
2. Enter the order number supplied by the laundry staff.
3. Submit the search.
4. Review the matching order information and current status.
5. If the tracker offers a feedback/complaint form, complete and submit it as instructed.

Customers do not need to create an account or use the staff login. The tracker should show only appropriate order information and must not expose customer phone numbers.

### C. Receipt

After staff creates an order, the receipt popup displays the order details. Check the order number and total before closing it. Actual printing is available only if print support is implemented in the current version.

## 10. Database and Migrations

Laravel migrations describe the database structure in version-controlled code. They allow a new database to be created without relying only on a copy of the developer's local database.

The application migrations include tables for:
- `users` — staff login accounts
- `services` — laundry service names and prices
- `addons` — optional add-ons and their prices
- `orders` — order details and statuses
- `order_addons` — add-ons associated with orders
- `feedback` — customer feedback or complaints
- `personal_access_tokens` — authentication tokens, if used by the configured application
- Laravel support tables such as `cache`, `jobs`, and `migrations`

The exact columns and relationships are defined in the migration files under `backend/database/migrations/`.

### Seeders

Seeders insert initial or sample records. Review `backend/database/seeders/DatabaseSeeder.php` to see which records will be inserted. A seeder is not a substitute for a database backup, and default credentials should never be assumed to match an existing account without testing.

### Optional PostgreSQL backup

A PostgreSQL SQL backup can be submitted separately if the instructor requires the database's current structure and sample data. Keep it outside the public Git repository unless it has been checked and deliberately sanitized. A full SQL dump can contain customer details, password hashes, access tokens, and other sensitive records—even if some records were created for testing.

For a clean demonstration, prefer a schema-and-seeder setup or a sanitized sample-data backup. Never share production data, real customer phone numbers, database passwords, password hashes, or authentication tokens unnecessarily.

## 11. Testing and Build Verification

Run the commands from the indicated folders.

### Backend tests

From `WashUp/backend`:

```bash
php artisan test
```

If the computer has multiple PHP installations, use the executable that has the required extensions enabled.

### Frontend production build

From the project root:

```bash
npm run build
```

A successful build checks whether Vite can compile the frontend. It does not by itself prove that database requests or every user workflow works.

### Manual workflow checks

Before submission, verify:
- Staff login succeeds with the demo account.
- Dashboard and order-management pages load.
- Creating an order saves it and displays the receipt popup.
- Service, weight, and add-on prices calculate as expected.
- The Customer Order Tracker finds an existing order by order number.
- The public tracker does not reveal customer phone numbers.
- Feedback/complaint submission works.
- Order statuses display and update as expected.
- Refreshing the page does not unexpectedly lose saved database records.

## 12. Troubleshooting

### `could not find driver` / PostgreSQL connection error
- Confirm PostgreSQL is running and `backend/.env` has the correct database settings.
- Confirm the PHP used in the terminal has `pdo_pgsql` enabled.
- If Windows has multiple PHP installations, check which one runs when you type `php`. Use the correct executable for Artisan.

### Frontend opens but data does not load
- Confirm the Laravel server is running.
- Check the frontend's actual API base URL configuration.
- Inspect the browser developer tools' Network and Console tabs.
- Check Laravel's logs under `backend/storage/logs/`.
- Confirm the database has been migrated and contains the sample order or service records needed for the test.

### Staff login fails
- Confirm the account exists in the local database.
- Use the verified demo credentials provided privately by the project team.
- Do not assume that the seeder changed the password of an existing user.
- Do not paste real passwords or access tokens into public issues or source code.

### A port is already in use
Stop the other process using that port or start the development server on a different available port, then use the URL printed in the terminal.

### Migration reports that a table already exists
Check the database configured in `backend/.env` and inspect migration status before trying destructive commands. Do not use `migrate:fresh` on a database containing data you want to keep; it drops tables and their data.

## 13. Deployment Guide

These steps describe a deployment process; they do **not** indicate that WashUp is currently hosted publicly.

1. Select hosting that supports the Laravel/PHP version and PostgreSQL.
2. Configure production environment variables and secure database credentials.
3. Install backend dependencies with Composer in the deployment environment.
4. Build the Vue frontend with `npm run build`.
5. Configure the web server to serve the built frontend and direct API requests to Laravel.
6. Set the production API base URL used by the frontend.
7. Run reviewed migrations against the production database.
8. Configure HTTPS, allowed origins, and other production security settings.
9. Keep `.env` files, SQL backups, tokens, and private customer data out of public web directories and repositories.
10. Test staff login, order creation, order tracking, feedback, and permissions after deployment.

The exact hosting steps depend on the selected provider and how the frontend and backend are hosted.

## 14. Design and Submission Materials

Include these items in the final submission:
- **GitHub repository:** complete codebase and project structure.
- **Migrations and seeders:** database setup files in the backend.
- **Database backup:** sanitized SQL backup only if requested by the instructor.
- **UI wireframes and implementation:** Figma design and screenshots of the implemented pages.
- **Setup guide:** this README.
- **Demo access:** provide a verified demo staff account to the instructor through an appropriate private channel.

**Figma design:** [Insert the WashUp Figma link]  
**UI screenshots:** [Add the submitted screenshots or their approved location]

### Demo staff account

For safety, do not place a real staff password in a public repository. If the instructor needs login access, create or verify a dedicated demo account and share its credentials privately. Fill in these details only after testing them:

- **Demo username:** [Insert verified demo username]
- **Demo password:** [Share privately with the instructor, not in a public README]

If the repository is private, grant the instructor access or submit the code through the instructor's approved channel.

## 15. Repository

- **GitHub repository:** https://github.com/xonikz-18/WashUp
- **Backend feature branch:** https://github.com/xonikz-18/WashUp/tree/feature-backend

---

**WashUp — Laundry Shop Management System**  
Developed for Web Systems and Technologies 1.
