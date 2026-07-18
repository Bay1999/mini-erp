# Mini ERP Application

A lightweight web-based Enterprise Resource Planning (ERP) application built with Laravel to manage sales, payments, and master data (users and items).

## Features

### 1. Dashboard
- **Interactive Widgets**: Real-time display of total transactions, total sales revenue in IDR, and total item quantities sold.
- **Dynamic Date Range Filters**: Filter widgets and charts dynamically by date range using Flatpickr.
- **Charts (Chart.js)**:
  - Monthly Sales Revenue (bar chart showing monthly sales trends, defaulting to 0 for months without transactions).
  - Item Quantity Sold (horizontal bar chart breakdown per item).

### 2. Master Data Management
- **User Management**:
  - Full CRUD operations with secure password hashing.
  - Role-based Access Control (RBAC) powered by **Spatie Laravel Permission**.
  - Interactive DataTables with search, sort, and pagination.
- **Item Management**:
  - Full CRUD operations including item image uploads.
  - Code generator auto-creates unique item codes (`ITM-XXXX`).
  - Safe-deletion lock: Items associated with any existing sales records cannot be modified or deleted.

### 3. Sales Module
- **Sales Transactions**:
  - Multi-item transaction forms with Select2 search and dynamic inputs.
  - Auto-generated sales invoice codes (`SLS-YYYYMMDD-XXXX`).
  - Validation: Prevents submitting a transaction without items.
  - Safe-deletion & edit lock: Sales records with associated payments cannot be edited or deleted.

### 4. Payment Module
- **Payment Operations**:
  - Automated payment code generator (`PMT-YYYYMMDD-XXXX`).
  - Support for **Partial Payments** (allows paying in installments until the total matches the sales grand total).
  - Serialized database transactions (`lockForUpdate`) to prevent overpayment concurrency issues.
  - Dynamic Invoice Status: Calculates status based on paid amounts (`Unpaid`, `Partially Paid`, or `Paid`).

---

## Tech Stack
- **Framework**: Laravel 11
- **Database**: MySQL / PostgreSQL
- **Frontend Core**: Tailwind CSS (for modern UI styling), Alpine.js
- **Frontend Libraries**:
  - jQuery 3.7 & DataTables
  - Chart.js (Interactive reporting charts)
  - Flatpickr (Custom datepicker)
  - Select2 (Dynamic searchable dropdowns)
- **ACL Package**: Spatie Laravel Permission

---

## Getting Started

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM

### Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Bay1999/mini-erp.git
   cd mini-erp
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment File**:
   Copy `.env.example` to `.env` and set up your database configurations:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run Migrations & Seed Database**:
   This runs the migrations and seeds the database with users, roles, permissions, items, and sample sales/payments data:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Compile Assets**:
   Run Vite development server:
   ```bash
   npm run dev
   ```

6. **Start Application**:
   ```bash
   php artisan serve
   ```

---

## Default Accounts (Seeded)

After running the database seeder, the following roles and default accounts are created:

### 1. Administrator (Full Access)
- **Email**: `admin@mail.com`
- **Password**: `123321`
- **Permissions**: Can manage users, items, sales, and payments.

### 2. Operator (Limited Access)
- **Email**: `operator@mail.com`
- **Password**: `123321`
- **Permissions**: Can manage items, sales, and payments (cannot view or manage users).