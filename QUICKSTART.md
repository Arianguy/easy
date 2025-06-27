# 🚀 CRM Quick Start Guide

Get your Modern CRM Application up and running in minutes!

## ⚡ Prerequisites

Make sure you have:

-   PHP 8.2+ installed
-   Composer installed
-   Node.js 18+ and NPM installed
-   MySQL 8.0+ running
-   Git installed

## 🏃‍♂️ Quick Setup (5 minutes)

### 1. Clone and Install

```bash
# Clone the repository
git clone <your-repository-url>
cd easy

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Configuration

```bash
# Create database (update credentials as needed)
mysql -u root -p -e "CREATE DATABASE crm_app"
```

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crm_app
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup

```bash
# Run migrations and seed sample data
php artisan migrate --seed
```

### 5. Build and Start

```bash
# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

## 🌐 Access Your CRM

Open your browser and go to: **http://localhost:8000**

## 🔑 Default Login Credentials

| Role              | Email                | Password |
| ----------------- | -------------------- | -------- |
| Area Manager      | admin@company.com    | password |
| Sales Manager 1   | manager1@company.com | password |
| Sales Manager 2   | manager2@company.com | password |
| Sales Executive 1 | sales1@company.com   | password |
| Sales Executive 2 | sales2@company.com   | password |

## 🎯 What You Get Out of the Box

### Sample Data Included:

-   **3 Branches**: Downtown, Uptown, and Westside locations
-   **10+ Users**: Area manager, sales managers, and executives
-   **60+ Customers**: Sample customer profiles across all branches
-   **100+ Leads**: Various lead statuses and priorities
-   **Opportunities**: Converted leads with revenue tracking
-   **Campaigns**: Marketing campaign examples
-   **Activities**: Customer interaction history

### Key Features Ready to Use:

-   ✅ Multi-branch CRM with data isolation
-   ✅ Role-based access control (3 user roles)
-   ✅ Complete customer lifecycle management
-   ✅ Lead tracking and conversion
-   ✅ Opportunity pipeline management
-   ✅ Campaign ROI tracking
-   ✅ Comprehensive analytics dashboard
-   ✅ Activity logging and history
-   ✅ Responsive design for mobile/desktop

## 🚀 Next Steps

### 1. Explore the Dashboard

-   Login as Area Manager to see all branches
-   Check conversion rates and pipeline values
-   Review team performance metrics

### 2. Test Lead Management

-   Create a new customer
-   Add leads for the customer
-   Convert a lead to opportunity
-   Track the sales pipeline

### 3. Try Different Roles

-   Login as Sales Manager to see branch-specific data
-   Login as Sales Executive to see assigned leads only

### 4. Customize for Your Business

-   Update branch information
-   Add your team members
-   Configure lead sources and statuses
-   Set up your product/service categories

## 🛠️ Development Mode

For development with hot reloading:

```bash
# In one terminal - start Laravel
php artisan serve

# In another terminal - start Vite dev server
npm run dev
```

## 📱 Mobile Access

The CRM is fully responsive! Access it from:

-   Desktop computers
-   Tablets
-   Mobile phones

## 🆘 Troubleshooting

### Common Issues:

**Database Connection Error:**

```bash
# Check your .env database settings
# Ensure MySQL is running
# Verify database exists
```

**Permission Errors:**

```bash
# Fix storage permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
```

**Missing Dependencies:**

```bash
# Clear and reinstall
composer install --no-cache
npm install --force
```

**Migration Issues:**

```bash
# Reset database if needed
php artisan migrate:fresh --seed
```

## 🎉 You're Ready!

Your Modern CRM Application is now running with:

-   ✅ Complete sample data
-   ✅ All features enabled
-   ✅ Multiple user roles
-   ✅ Responsive design
-   ✅ Analytics dashboard

Start exploring and customizing for your business needs!

## 📚 Need More Help?

-   Check the full [README.md](README.md) for detailed documentation
-   Review the codebase for customization examples
-   Test different user roles to understand permissions

---

**Happy CRM-ing! 🎯**
