# NewEvixERP 🚀

<div align="center">
## 📖 About

**NewEvixERP** is a modular and comprehensive Enterprise Resource Planning (ERP) system designed to streamline business operations. Built with the Laravel framework, it provides a robust, scalable, and modern solution for managing core business functions. The system is divided into key modules, including HR, Store, and Operations, to provide a tailored experience.
</div>
---

## ✨ Features

This ERP system is built on a modular architecture using `nwidart/laravel-modules`, allowing for clear separation of concerns and scalability.

### 🏢 BasicData Module
- Core system configurations.
- Management of foundational data like company info, branches, and currencies.

### 👥 HR (Human Resources) Module
- Complete employee profiles and records management.
- Attendance tracking with import/export functionality.
- Leave management with approval workflows.
- Payroll and salary management.

### 🏪 Store Module
- Inventory and stock management.
- Product cataloging and categorization.
- Supplier and purchase order management.

### ⚙️ Operation Module
- Management of daily business operations and workflows.
- (Add more specific features of the Operation module as it's developed)

### 📊 Dashboard & Reports
- Real-time business metrics and KPIs.
- Customizable report generation for all modules.
- Data visualization with charts.

### 🔐 Security & Access Control
- Role-based permissions powered by `spatie/laravel-permission`.
- Secure authentication using Laravel Sanctum.
- Activity logging for audit trails.

---

## 🛠️ Tech Stack

| Technology | Description |
|------------|-------------|
| **Framework** | Laravel 10+ |
| **Language** | PHP 8.1+ |
| **Backend UI**| Livewire |
| **Frontend** | Blade Templates, AdminLTE 4, Bootstrap 4 |
| **Build Tool**| Vite |
| **Database** | MySQL 5.7+ / MariaDB 10+ |
| **Authentication**| Laravel Sanctum |
| **API Docs** | L5 Swagger |
| **Key Packages**| `nwidart/laravel-modules`, `spatie/laravel-permission`, `maatwebsite/excel` |

---

## ⚙️ Installation

### Prerequisites

Before you begin, ensure you have the following installed:
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL 5.7+ or MariaDB 10+
- Git

### Step-by-Step Setup

#### 1️⃣ Clone the Repository
```bash
# Replace with your repository's URL
git https://github.com/dhiaamostafa46/NewEvixERP.git
cd NewEvixERP 
```

#### 2️⃣ Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install NPM packages
npm install
```

#### 3️⃣ Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit your `.env` file with your database, mail, and other service credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=new_evix_erp
DB_USERNAME=root
DB_PASSWORD=
```

#### 4️⃣ Database Setup
```bash
# Run migrations and seed the database with initial data
php artisan migrate --seed
```
#### 5️⃣ Build Assets & Start Development Servers
```bash
# Run the Vite development server for frontend assets
npm run dev

# In a separate terminal, run the PHP development server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## 🔧 Useful Commands

```bash
# Clear all application cache
php artisan optimize:clear

# Run database migrations and seeders
php artisan migrate:fresh --seed

# List all registered routes
php artisan route:list

# Run the test suite
php artisan test
```
