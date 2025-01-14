# T-VIBES
Taal Visitor Information and Booking System (T-VIBES) is a web application that showcases the tourist attractions and destinations of Taal, Batangas and also allows tourists to book a tour trip. Website name is still subject to change.
<br>
Folder Structure Guide:
<br>
/my-cms-reservation-system
<br>
|-- /public                   # Publicly accessible files (web root)
<br>
|   |-- index.php             # Entry point of the site (could be a front-page or router)
<br>
|   |-- /assets               # Static assets (images, CSS, JS)
<br>
|   |   |-- /images           # Images used on the site
<br>
|   |   |   |-- logo.png      # Logo for the site
<br>
|   |   |   |-- favicon.ico   # Favicon
<br>
|   |   |-- /styles           # CSS, SCSS, or other stylesheets
<br>
|   |   |   |-- main.css      # Main stylesheet
<br>
|   |   |-- /scripts          # JS files for front-end functionality
<br>
|   |   |   |-- app.js        # Custom JS logic for the front-end (form validation, etc.)
<br>
|   |-- /uploads              # Folder to store uploaded images, files, etc.
<br>
|   |   |-- product-images    # Images uploaded by users (e.g., for reservations, products)
<br>
|-- /src                      # Application source code (PHP, logic, controllers)
<br>
|   |-- /config               # Configuration files (database, environment settings)
<br>
|   |   |-- database.php      # Database connection and settings
<br>
|   |   |-- config.php        # General app configuration (app name, debug mode)
<br>
|   |   |-- routes.php        # Routing configurations
<br>
|   |-- /controllers          # PHP controllers for handling business logic
<br>
|   |   |-- ContentController.php   # Handles content-related logic (CRUD for posts, etc.)
<br>
|   |   |-- ReservationController.php # Handles reservation logic (creating, viewing reservations)
<br>
|   |   |-- UserController.php       # Handles user-related actions (authentication, permissions)
<br>
|   |-- /models               # PHP models for interacting with the database
<br>
|   |   |-- ContentModel.php  # Represents content in the database (posts, pages)
<br>
|   |   |-- ReservationModel.php  # Represents reservation data in the DB (rooms, events)
<br>
|   |   |-- UserModel.php      # Handles user data and permissions in the DB
<br>
|   |-- /views                # PHP views (templates) for rendering dynamic content
<br>
|   |   |-- /templates        # Common templates used across pages (header, footer, etc.)
<br>
|   |   |   |-- header.php    # Header template (for consistency across pages)
<br>
|   |   |   |-- footer.php    # Footer template (for consistency across pages)
<br>
|   |   |-- /admin            # Views for the admin dashboard (content management, reservations)
<br>
|   |   |   |-- dashboard.php # Dashboard page for the admin user
<br>
|   |   |   |-- content-list.php # List of content to manage (posts, events, etc.)
<br>
|   |   |   |-- reservation-list.php # Reservation management page
<br>
|   |   |-- /frontend         # Views for the public-facing site (guests, users)
<br>
|   |   |   |-- home.php      # Home page that shows content like blog posts or events
<br>
|   |   |   |-- reservation-form.php # Page where users can make reservations
<br>
|-- /db                       # Database migrations, seed data, and schema files
<br>
|   |-- schema.sql            # SQL file with the database schema
<br>
|   |-- migrations            # Folder for migration scripts (e.g., to create tables)
<br>
|   |   |-- 2025_01_01_create_reservations_table.php # Migration script for creating reservation table
<br>
|   |   |-- 2025_01_02_create_content_table.php     # Migration script for content table
<br>
|-- /vendor                   # Third-party libraries and dependencies (composer)
<br>
|   |-- /autoload.php         # Composer autoloader
<br>
|-- .env                      # Environment variables (DB credentials, API keys, etc.)
<br>
|-- .gitignore                # Git ignore file to exclude unnecessary files
<br>
|-- composer.json             # Composer dependency manager file
<br>
|-- README.md                 # Project documentation (setup, usage, etc.)
