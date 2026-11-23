# Unique Closet — E-Commerce Website

## Setup
1. Install XAMPP, start Apache + MySQL.
2. Import `database.sql` into phpMyAdmin (creates DB `unique_closet` + seed data).
3. Verify `includes/db.php` credentials match your MySQL (default: root / blank).
4. Open http://localhost/unique_closet/

## Test Credentials
- Admin: admin@uniquecloset.test / admin123
- Customer: register at register.php

## Features
- Customer: register/login/logout, browse/search/filter, cart, COD checkout, order tracking, cancel, reviews
- Admin: product/category CRUD, order status management, customer list, sales report

## Tech Stack
PHP, MySQL, Bootstrap 5. Prepared statements (PDO), hashed passwords, CSRF tokens.

## Security Notes
- Passwords hashed with `password_hash()` (bcrypt)
- All queries use PDO prepared statements (SQL injection safe)
- All output escaped via `e()` (XSS safe)
- CSRF tokens on all POST forms
- Sessions regenerated on login