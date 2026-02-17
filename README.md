# PHPAssignment4

SportsPro Technical Support application for MWD4A (PHPSQL).

This project continues the SportsPro web application and expands on the
functionality built in previous assignments. The application is developed
using PHP, MySQL, Bootstrap 5, and XAMPP in a structured MVC-style layout.

## Features Implemented

### Product Management
- Add, update, and delete products
- Improved date handling (accepts multiple date formats)
- Release dates displayed in `m-d-yyyy` format (no leading zeros)

### Technician Management
- Add and manage technicians
- Secure password hashing using `password_hash()`

### Customer Management
- Search customers by last name
- View and update customer details
- Country selection implemented as a dynamic drop-down list
  (data pulled from the `countries` table)

### Product Registration
- Customer login using sessions
- Registered products stored in the `registrations` table
- Duplicate registration prevention
- Session-based login (no hidden customerID fields required)
- Logout functionality

### Incident Management
- Retrieve customer by email
- Create new incidents
- Product drop-down restricted to products registered to the customer
- Confirmation message displayed after successful incident creation

## Technical Highlights
- Switch-based controller logic
- Prepared statements for all database interactions
- Session-based authentication
- Bootstrap 5 responsive UI
- Structured folder layout (`models`, `views`, etc.)
- Configurable BASE_URL constant for portability

## Environment
- PHP 8+
- MySQL (tech_support database)
- XAMPP (Apache + MySQL)
- GitHub version control

---

This assignment further strengthens controller logic, session handling,
form validation, database relationships, and overall application structure.
