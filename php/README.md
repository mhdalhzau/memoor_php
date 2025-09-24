# Database Migrations - SPBU Management System

This folder contains all database migrations for the SPBU Management System.

## Files Structure

- `001_create_users_table.sql` - Creates users table with authentication data
- `002_create_stores_table.sql` - Creates stores table for gas station locations
- `003_create_user_stores_table.sql` - Creates junction table for user-store assignments
- `004_create_attendance_table.sql` - Creates attendance tracking table
- `005_create_sales_table.sql` - Creates sales records table
- `006_create_cashflow_table.sql` - Creates cashflow management table
- `007_create_payroll_table.sql` - Creates payroll processing table
- `008_create_proposals_table.sql` - Creates proposals/suggestions table
- `009_create_overtime_table.sql` - Creates overtime tracking table
- `010_insert_sample_data.sql` - Inserts initial sample data for testing

## Running Migrations

### Prerequisites
1. Make sure your MySQL database is created and accessible
2. Update the database configuration in `../php/config/config.php`
3. Set the following environment variables or update the config:
   - `MYSQL_HOST` (default: localhost)
   - `MYSQL_PORT` (default: 3306)
   - `MYSQL_DATABASE` (default: spbu_management)
   - `MYSQL_USER` (default: root)
   - `MYSQL_PASSWORD`

### Commands

```bash
# Navigate to migrations folder
cd migrated_pos

# Run all pending migrations
php migration_runner.php migrate

# Check migration status
php migration_runner.php status

# Rollback last migration
php migration_runner.php rollback

# Rollback multiple migrations
php migration_runner.php rollback 3
```

## Database Schema

### Users Table
- Stores user authentication and basic info
- Roles: staff, manager, administrasi
- Uses UUID for primary key

### Stores Table
- Gas station locations
- Store-specific settings (hours, timezone)
- Integer primary key

### User-Stores Junction
- Many-to-many relationship between users and stores
- Allows users to work at multiple locations

### Core Business Tables
- **Attendance**: Employee time tracking
- **Sales**: Daily sales reporting
- **Cashflow**: Financial transactions
- **Payroll**: Employee compensation
- **Proposals**: Employee suggestions
- **Overtime**: Overtime hour tracking

## Sample Data

The migration includes sample users:
- Manager (manager@spbu.com / manager123)
- Administrator (admin@spbu.com / admin123)
- Staff: Putri, Hafiz, Endang (email@spbu.com / name123)

## Engine Choice

All tables use MyISAM engine for:
- Better performance for read-heavy operations
- Simplified structure without foreign key constraints
- Indexes are used for relationships and fast joins

## Security Notes

- All passwords are hashed using PHP's password_hash() with bcrypt
- UUIDs are used for sensitive record identification
- Database access should be restricted to application layer only