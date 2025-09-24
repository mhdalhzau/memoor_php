# SPBU Management System - PHP Conversion Setup Guide

## 📁 Project Structure

```
migrated_pos/
├── 📄 SQL Migration Files
│   ├── 001_create_users_table.sql
│   ├── 002_create_stores_table.sql
│   ├── 003_create_user_stores_table.sql
│   ├── 004_create_attendance_table.sql
│   ├── 005_create_sales_table.sql
│   ├── 006_create_cashflow_table.sql
│   ├── 007_create_payroll_table.sql
│   ├── 008_create_proposals_table.sql
│   ├── 009_create_overtime_table.sql
│   └── 010_insert_sample_data.sql
│
├── 🔧 Migration Tools
│   ├── migration_runner.php
│   └── README.md
│
└── 🐘 PHP Backend
    └── php/
        ├── index.php (Main entry point)
        ├── config/
        │   └── config.php (Database & app configuration)
        └── includes/
            ├── database.php (Database connection)
            ├── auth.php (Authentication system)
            ├── router.php (API routing)
            └── api/
                ├── users.php (User management API)
                ├── stores.php (Store management API)
                ├── attendance.php (Attendance tracking API)
                ├── sales.php (Sales recording API)
                ├── cashflow.php (Financial transactions API)
                ├── payroll.php (Employee payroll API)
                ├── proposals.php (Employee suggestions API)
                └── overtime.php (Overtime tracking API)
```

## 🚀 Quick Setup

### 1. Database Setup
```bash
# 1. Create MySQL database
mysql -u root -p
CREATE DATABASE spbu_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2. Update database credentials in php/config/config.php
# Set: MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD

# 3. Run migrations
cd migrated_pos
php migration_runner.php migrate
```

### 2. PHP Server Setup
```bash
# Option 1: Built-in PHP server
cd migrated_pos/php
php -S localhost:8080 index.php

# Option 2: Apache/Nginx
# Point document root to migrated_pos/php/
# Ensure mod_rewrite is enabled for clean URLs
```

### 3. Test the API
```bash
# Check API status
curl http://localhost:8080/

# Test login
curl -X POST http://localhost:8080/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"manager@spbu.com","password":"manager123"}'
```

## 🔗 API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user info

### Core Resources
- `GET|POST|PUT|DELETE /api/users` - User management
- `GET|POST|PUT|DELETE /api/stores` - Store management
- `GET|POST|PUT|PATCH /api/attendance` - Attendance tracking
- `GET|POST|DELETE /api/sales` - Sales recording
- `GET|POST|PUT|DELETE /api/cashflow` - Financial transactions
- `GET|POST|PUT|DELETE /api/payroll` - Employee payroll
- `GET|POST|PUT|DELETE /api/proposals` - Employee suggestions
- `GET|POST|PUT|PATCH|DELETE /api/overtime` - Overtime tracking

## 🔐 Sample User Accounts

| Email | Password | Role | Access |
|-------|----------|------|---------|
| manager@spbu.com | manager123 | manager | Full store management |
| admin@spbu.com | admin123 | administrasi | Full system access |
| putri@spbu.com | putri123 | staff | Basic employee features |
| hafiz@spbu.com | hafiz123 | staff | Basic employee features |
| endang@spbu.com | endang123 | staff | Basic employee features |

## 🛠️ Database Features

### MyISAM Engine Benefits
- ✅ **Fast reads** - Optimized for reporting and analytics
- ✅ **No foreign key overhead** - Simplified relationships
- ✅ **Indexes only** - Fast joins without constraint penalties
- ✅ **Smaller footprint** - Efficient storage

### Database Structure
- **Users & Authentication** - Secure password hashing with bcrypt
- **Multi-store Support** - Users can be assigned to multiple stores
- **Role-based Access** - Staff, Manager, Administrator roles
- **Business Modules** - Attendance, Sales, Payroll, Cashflow tracking
- **UUID Primary Keys** - Secure, globally unique identifiers

## 🔧 Configuration

### Environment Variables
```bash
# MySQL Database
MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_DATABASE=spbu_management
MYSQL_USER=your_user
MYSQL_PASSWORD=your_password

# Application
DEBUG_MODE=true
SESSION_LIFETIME=86400
```

### PHP Requirements
- PHP 8.0+ with PDO MySQL extension
- BCrypt support for password hashing
- Session handling enabled

## 🚦 Development Workflow

### Making Database Changes
```bash
# 1. Create new migration file
# migrated_pos/011_your_changes.sql

# 2. Run migrations
php migration_runner.php migrate

# 3. Check status
php migration_runner.php status
```

### Adding New API Endpoints
1. Create new API class in `php/includes/api/`
2. Add route handler in `php/includes/router.php`
3. Follow existing authentication patterns
4. Use consistent error handling and response formats

## 📊 Conversion Summary

### ✅ Successfully Converted
- **Database Schema** - From PostgreSQL to MySQL/MyISAM
- **Authentication System** - PHP sessions with bcrypt
- **All API Endpoints** - Complete REST API coverage
- **Business Logic** - User, Store, Attendance, Sales, etc.
- **Security Features** - Role-based access control
- **Migration System** - Automated database setup

### 🔄 Frontend Integration
To integrate with existing React frontend:
1. Update API base URL to PHP server
2. Modify authentication flow for PHP sessions
3. Update CORS settings in PHP for React dev server
4. Test all existing frontend functionality

## 🎯 Next Steps

1. **Test API endpoints** with your existing frontend
2. **Configure production server** (Apache/Nginx)
3. **Set up SSL/HTTPS** for production
4. **Configure backup strategy** for MySQL database
5. **Monitor performance** and optimize queries as needed

---

**🎉 Your Node.js/PostgreSQL SPBU application has been successfully converted to PHP/MySQL!**