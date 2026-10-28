# WashUp — Laundry Shop Management System

WashUp is a web-based Laundry Shop Management System developed for **Web Systems and Technologies 1**. It helps laundry staff manage orders, calculate laundry charges, maintain service and add-on prices, update order statuses, and review customer feedback. Customers can check their order through the Customer Order Tracker without creating an account.

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
11. [Testing](#11-testing)
12. [Troubleshooting](#12-troubleshooting)
13. [Deployment](#13-deployment)
14. [Design and Submission Materials](#14-design-and-submission-materials)
15. [Repository](#15-repository)

## 1. Project Overview

- **Project name:** WashUp
- **Project type:** Web-based Laundry Shop Management System
- **Course:** Web Systems and Technologies 1

WashUp brings order details, service prices, add-ons, order statuses, and customer feedback into one application. It includes a staff management area and a public Customer Order Tracker.

## 2. Users and Access

## Laundry staff / admin

Laundry staff sign in to access the management features. Depending on the available features, they can create orders, maintain service and add-on information, update order statuses, and review reports and customer feedback.

## Customers

Customers do not log in. They use the Customer Order Tracker to look up an order using the order number provided by the laundry staff. Customers can also submit feedback or complaints through the customer-facing form.

## Privacy

- The public tracker should not display customer phone numbers.
- Phone numbers collected for staff contact should remain private.
- Do not commit passwords, database credentials, API secrets, or access tokens to the repository.
- If the instructor needs to test staff login, provide verified demo credentials privately.

## 3. Features

- **Staff login:** Sign-in for laundry staff.
- **Dashboard:** Overview of laundry-shop activity.
- **Order management:** Create and manage laundry orders.
- **Order statuses:** Pending, Ready for Pickup, and Claimed.
- **Price calculation:** Calculate charges based on the selected service, laundry weight, and applicable add-ons.
- **Service management:** Maintain laundry services and prices.
- **Add-on management:** Maintain optional add-ons, such as fabric conditioner or extra drying, if configured.
- **Customer Order Tracker:** Search for an order using its order number without logging in.
- **Feedback and complaints:** Allow customers to submit feedback or complaints.
- **Reports:** View recorded laundry-operation information.
- **Receipt popup:** Display order details after an order is created. Printing is available only if print support is implemented.

Use the running application to confirm which features are available in the submitted version.

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

The frontend and backend are kept in one repository. The Customer Order Tracker is part of the same frontend application.

```text
WashUp/
├── backend/              # Laravel backend
│   ├── app/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── routes/
│   ├── .env.example
│   └── composer.json
├── public/                # Public frontend assets
├── src/                   # Vue pages and components
├── package.json
├── vite.config.js
└── README.md
```

This is a simplified view of the project structure. Refer to the repository for the complete list of files.

## 6. Prerequisites

Install the following on the computer that will run WashUp:

- Git
- Node.js and npm
- PHP compatible with the Laravel project
- Composer
- PostgreSQL
- Visual Studio Code or another code editor

The PHP installation must have the `pdo_pgsql` extension enabled. If more than one PHP installation is present on Windows, use the one with PostgreSQL support enabled.

## 7. Installation and Setup

These instructions describe a local development setup on Windows.

## Step 1: Clone the repository

Open PowerShell and run:

```powershell
git clone https://github.com/xonikz-18/WashUp.git
cd WashUp
git checkout feature-backend
```

Use the branch specified by the project team or instructor if it differs from `feature-backend`.

## Step 2: Create the database

1. Start PostgreSQL.
2. Open pgAdmin or another PostgreSQL client.
3. Create a database named `washup_db`.
4. Keep your database username and password available for local configuration.

## Step 3: Configure the Laravel backend

From the project root, run:

```powershell
cd backend
composer install
```

Create the local environment file if it does not already exist:

```powershell
Copy-Item .env.example .env
```

If `backend/.env` already exists, do not overwrite it. Open it and check the database settings:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=washup_db
DB_USERNAME=postgres
DB_PASSWORD=your_local_postgres_password
```

Replace the example password with the password for your local PostgreSQL account. Do not commit `backend/.env` to GitHub.

Generate the application key:

```powershell
php artisan key:generate
```

Run the migrations:

```powershell
php artisan migrate
```

To insert the initial records defined by the project seeder, run:

```powershell
php artisan db:seed
```

Only seed a database when appropriate. Back up any database containing data you need to keep before running database-changing commands. Seeding does not necessarily reset an existing staff account's password, so verify the demo login separately.

If the terminal uses a PHP installation without PostgreSQL support, run Artisan with the PHP executable that has `pdo_pgsql` enabled. For the development computer used for this project, that executable was:

```powershell
C:\php\php.exe artisan key:generate
C:\php\php.exe artisan migrate
C:\php\php.exe artisan db:seed
```

Run these commands from the `backend` folder. Use this path only if PHP is installed at that location on your computer.

## Step 4: Install the frontend dependencies

Open a second terminal at the project root (`WashUp`, not `WashUp/backend`) and run:

```powershell
npm install
```

## Step 5: Check the frontend API configuration

The frontend needs to send API requests to the Laravel backend. Check the frontend source and its environment configuration for the API base URL used by this project. If it needs to be changed for local use, set it to the correct Laravel address and restart Vite.

Use the environment variable name referenced by the actual code rather than adding a new one without checking.

## 8. Running WashUp

Keep two terminals open.

**Backend terminal:** From `WashUp/backend`, run:

```powershell
C:\php\php.exe artisan serve
```

**Frontend terminal:** From the root `WashUp` folder, run:

```powershell
npm run dev
```

Open the local URL printed by Vite, commonly `http://localhost:5173`.

The Customer Order Tracker is part of the same frontend application. Open it using the route configured in the Vue application; do not assume a URL path without checking the current routes.

## 9. How to Use the System

## Laundry staff

1. Open the WashUp website.
2. Sign in with a valid staff account.
3. Open the dashboard.
4. Go to order management and create an order.
5. Enter the customer and laundry details requested by the form.
6. Select the service, enter the laundry weight, and choose any applicable add-ons.
7. Check the calculated total and save the order.
8. Review the receipt popup.
9. Update the order status as appropriate.
10. Use the available service, add-on, reports, and feedback features as needed.

Check the order details before saving and follow the validation rules shown by the application.

## Customers

1. Open the Customer Order Tracker page.
2. Enter the order number provided by the laundry staff.
3. Submit the search and review the matching order information.
4. Use the feedback or complaint form if it is available.

Customers do not need to create an account or use the staff login. The tracker should not display customer phone numbers.

## Receipt

After an order is created, the receipt popup displays the order details. Check the order number and total before closing the popup. Printing depends on the functionality implemented in the current version.

## 10. Database and Migrations

Laravel migrations define the database structure in code. They allow the database tables to be created on another development computer.

The migrations include tables for:

- `users` — staff accounts
- `services` — laundry services and prices
- `addons` — optional add-ons and prices
- `orders` — order details and statuses
- `order_addons` — add-ons associated with orders
- `feedback` — customer feedback and complaints
- `personal_access_tokens` — authentication tokens, if used
- Laravel support tables such as `cache`, `jobs`, and `migrations`

The migration files under `backend/database/migrations/` define the actual columns and relationships.

## Seeders

Seeders insert the initial or sample records defined by the project. Check `backend/database/seeders/DatabaseSeeder.php` before running them. A seeder is not a replacement for a database backup and may not change the password of an existing account.

## Database backup

If a database backup is required for submission, check the SQL file before sharing it. A full dump may contain customer information, password hashes, tokens, or other data. Do not upload a backup containing sensitive information to a public repository.

## 11. Testing

Run the commands from the indicated folders.

## Backend tests

From `WashUp/backend`:

```powershell
C:\php\php.exe artisan test
```

Use the PHP executable with the required extensions enabled.

### Frontend build

From the root `WashUp` folder:

```powershell
npm run build
```

A successful build confirms that Vite can compile the frontend. It does not by itself confirm that all database requests and user workflows work correctly.

## Manual checks

Before submission, check the workflows that are implemented:

- Staff login works with the verified demo account.
- The dashboard and order-management pages load.
- Creating an order saves it and opens the receipt popup.
- Service, weight, and add-on prices are calculated correctly.
- The Customer Order Tracker finds an order by order number.
- The public tracker does not display customer phone numbers.
- Feedback or complaint submission works.
- Order statuses display and update correctly.
- Saved orders remain available after refreshing the page.

## 12. Troubleshooting

## PostgreSQL driver or connection error

- Check that PostgreSQL is running.
- Confirm the database settings in `backend/.env`.
- Confirm that the PHP executable used by the terminal has `pdo_pgsql` enabled.
- If multiple PHP installations are present, use the correct executable for Artisan.

## Frontend opens but data does not load

- Confirm that the Laravel server is running.
- Check the API base URL used by the frontend.
- Check the browser's Network and Console tabs.
- Review Laravel logs under `backend/storage/logs/`.
- Confirm that the database has been migrated and contains the records needed for the workflow.

## Staff login fails

- Confirm that the staff account exists in the configured database.
- Use verified credentials for the demo account.
- Do not assume that running the seeder changed an existing account's password.

## A port is already in use

Stop the process using the port or start the development server on another available port. Use the URL printed in the terminal.

## A migration reports that a table already exists

Check which database is configured in `backend/.env` and inspect the migration status before making changes. Do not run `migrate:fresh` on a database containing data you want to keep, because it drops the existing tables and their data.

## 13. Deployment

The application must be configured for the selected hosting environment before it can be used publicly.

1. Choose hosting that supports the PHP/Laravel version used by the backend and provides PostgreSQL.
2. Configure the production environment variables and database credentials.
3. Install backend dependencies with Composer.
4. Build the Vue frontend with `npm run build`.
5. Configure the web server to serve the frontend and route API requests to Laravel.
6. Set the frontend API base URL for the production environment.
7. Review and run the required migrations against the production database.
8. Configure HTTPS, allowed origins, and other required security settings.
9. Keep environment files, credentials, tokens, and private customer data out of public directories and repositories.
10. Test login, order creation, order tracking, feedback, and access permissions after deployment.

These are general deployment steps. They do not mean the application has already been deployed to a public hosting service.

## 14. Project References and Demo Access

## Project References

- **GitHub Repository:** https://github.com/xonikz-18/WashUp
- **Figma Design:** https://www.figma.com/design/zYo1x4esJNUZyeoJFbgirI/washUP-draft?node-id=0-1&t=CMNo1Pqpx6TCIUKf-0
- **UI Screenshots:** Included in the `ui screenshots` folder in the project submission ZIP.

## Demo Staff Account

The following account is provided for testing the staff login:

- **Username:** `admin`
- **Password:** `Admin123!`

Please ensure that these credentials match the submitted database and successfully sign in before testing the system.
