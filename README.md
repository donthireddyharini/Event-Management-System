# Event-Management-System
# Event Management System

A PHP-based web application for managing events, venues, and bookings. The system allows users to browse upcoming events, reserve venues, register for events, and submit reviews, while administrators can manage events, bookings, users, and venue details from a dashboard.

## Features

- Upcoming event listing and details view
- Venue listing and booking requests
- User registration and login flow
- Admin dashboard for managing events, venues, bookings, and users
- Review submission for clients and event attendees
- Responsive interface built with Bootstrap and jQuery
- My Bookings section for logged-in users

## Tech Stack

- PHP
- MySQL
- JavaScript
- Bootstrap
- jQuery

## Project Structure

- `index.php` – main entry point for the frontend
- `home.php` – homepage displaying upcoming events
- `booking.php` – booking flow for venues/events
- `venue.php` – venue pages
- `admin/` – admin panel and database logic
- `database/event_db.sql` – database schema and sample data
- `assets/` and `css/` – static frontend files

## Prerequisites

Before running the project, ensure that you have:

- PHP 7.4 or newer
- MySQL or MariaDB
- Apache/XAMPP/WAMP/Laragon
- A browser for testing the app

## Setup Instructions

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/Event_Management_System.git
   ```

2. Place the project inside your local web server directory, such as:

   - XAMPP: `htdocs`
   - WAMP: `www`
   - Laragon: `www`

3. Create and import the database:

   - Open phpMyAdmin
   - Create a database named `event_db`
   - Import the SQL script from `database/event_db.sql`

4. Update the database credentials in `admin/db_connect.php`:

   ```php
   $db_host = 'localhost';
   $db_user = 'root';
   $db_pass = '';
   $db_name = 'event_db';
   ```

5. Start your local server and open:

   ```text
   http://localhost/Event_Management_System/
   ```

## Default Admin Login

- Username: `admin`
- Password: `admin`

## Notes

This project includes sample data and a production-style database configuration. For local testing, update the credentials in `admin/db_connect.php` to match your local MySQL environment.

## License

This project is intended for educational and personal use. Please verify licensing before using it in a production environment.

## Author

Developed by Donthireddy Harini.
Free Download Source Code "Event_Management_System"

FIRST Download

1.XAMPP

2."TEXT EDITOR" NOTEPAD++ OR SUBLIME TEXT 3 / ETC.

3"Event_Management_System"

4. Download the zip file/ download winrar

5. Extract the file and copy "Event_Management_System" folder

6.Paste inside root directory/ where you install xammp local disk C: drive D: drive E: paste: (for xampp/htdocs, 

7. Open PHPMyAdmin (http://localhost/phpmyadmin)

8. Create a database with name event_db

6. Import event_db.sql file(given inside the zip package in SQL file folder)

7.Run the script http://localhost/Event_Management_System

Homepage: http://eventmanagementsystem.site.je/

Admin Login: http://eventmanagementsystem.site.je/admin/login.php

Username: admin 

 Password: admin123

**LOGIN DETAILS** 
Admin
user: admin
pass: admin123

****** https:1sourcecodr.blogspot.com ******
Subcribe my You tube Channel **** 1 Source code ****
