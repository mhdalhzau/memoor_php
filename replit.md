# SPBU Management System

## Overview

This is a PHP-based management system for SPBU (gas stations) that handles comprehensive business operations including employee management, attendance tracking, sales recording, financial transactions, payroll processing, and employee suggestions. The system provides a REST API backend with a structured database schema designed to support multi-location gas station operations.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Backend Architecture
- **PHP REST API**: Built with vanilla PHP using a modular structure
- **Routing System**: Custom router (`router.php`) handles API endpoint routing
- **Authentication**: JWT-based authentication system for secure access
- **Database Layer**: Direct MySQL connection with prepared statements for security
- **Modular Design**: Each business domain (users, stores, attendance, etc.) has dedicated API modules

### Database Architecture
- **MySQL Database**: Primary data storage with utf8mb4 character set
- **Migration System**: Sequential SQL migration files with a PHP migration runner
- **Normalized Schema**: Proper foreign key relationships between entities
- **Junction Tables**: User-store assignments for multi-location access control

### Key Design Patterns
- **API-First Architecture**: Clean separation between backend logic and data access
- **Domain-Driven Design**: Business logic organized by functional domains
- **Configuration Management**: Centralized configuration with environment variable support
- **Database Migrations**: Version-controlled schema changes with rollback support

### Core Business Modules
1. **User Management**: Employee authentication and role-based access
2. **Store Management**: Multi-location gas station administration
3. **Attendance Tracking**: Employee clock-in/out with location validation
4. **Sales Recording**: Transaction logging and reporting
5. **Cashflow Management**: Financial transaction tracking
6. **Payroll Processing**: Employee compensation calculation
7. **Proposals System**: Employee suggestion and feedback management
8. **Overtime Tracking**: Extended work hour monitoring

### Security Features
- **Prepared Statements**: Protection against SQL injection
- **Authentication Layer**: Secure user session management
- **Role-Based Access**: User-store assignment controls

## External Dependencies

### Database
- **MySQL**: Primary database server (configurable host/port)
- **Character Set**: UTF8MB4 for full Unicode support

### PHP Requirements
- **Core PHP**: Vanilla PHP implementation (no major frameworks)
- **MySQL Extension**: For database connectivity
- **JSON Support**: For API response formatting

### Environment Configuration
- **MYSQL_HOST**: Database server hostname (default: localhost)
- **MYSQL_PORT**: Database server port (default: 3306)
- **MYSQL_DATABASE**: Database name (default: spbu_management)
- **MYSQL_USER**: Database username (default: root)
- **MYSQL_PASSWORD**: Database password (environment-specific)

### Development Tools
- **Migration Runner**: Custom PHP tool for database schema management
- **SQL Migration Files**: Sequential schema changes with sample data