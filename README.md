# T-VIBES
Taal Visitor Information and Booking System (T-VIBES) is a web application that showcases the tourist attractions and destinations of Taal, Batangas and also allows tourists to book a tour trip. Website name is still subject to change.
Folder Structure Guide:
/my-cms-reservation-system
|-- /public                   # Publicly accessible files (web root)
|   |-- index.php             # Entry point of the site (could be a front-page or router)
|   |-- /assets               # Static assets (images, CSS, JS)
|   |   |-- /images           # Images used on the site
|   |   |   |-- logo.png      # Logo for the site
|   |   |   |-- favicon.ico   # Favicon
|   |   |-- /styles           # CSS, SCSS, or other stylesheets
|   |   |   |-- main.css      # Main stylesheet
|   |   |-- /scripts          # JS files for front-end functionality
|   |   |   |-- app.js        # Custom JS logic for the front-end (form validation, etc.)
|   |-- /uploads              # Folder to store uploaded images, files, etc.
|   |   |-- product-images    # Images uploaded by users (e.g., for reservations, products)
|-- /src                      # Application source code (PHP, logic, controllers)
|   |-- /config               # Configuration files (database, environment settings)
|   |   |-- database.php      # Database connection and settings
|   |   |-- config.php        # General app configuration (app name, debug mode)
|   |   |-- routes.php        # Routing configurations
|   |-- /controllers          # PHP controllers for handling business logic
|   |   |-- ContentController.php   # Handles content-related logic (CRUD for posts, etc.)
|   |   |-- ReservationController.php # Handles reservation logic (creating, viewing reservations)
|   |   |-- UserController.php       # Handles user-related actions (authentication, permissions)
|   |-- /models               # PHP models for interacting with the database
|   |   |-- ContentModel.php  # Represents content in the database (posts, pages)
|   |   |-- ReservationModel.php  # Represents reservation data in the DB (rooms, events)
|   |   |-- UserModel.php      # Handles user data and permissions in the DB
|   |-- /views                # PHP views (templates) for rendering dynamic content
|   |   |-- /templates        # Common templates used across pages (header, footer, etc.)
|   |   |   |-- header.php    # Header template (for consistency across pages)
|   |   |   |-- footer.php    # Footer template (for consistency across pages)
|   |   |-- /admin            # Views for the admin dashboard (content management, reservations)
|   |   |   |-- dashboard.php # Dashboard page for the admin user
|   |   |   |-- content-list.php # List of content to manage (posts, events, etc.)
|   |   |   |-- reservation-list.php # Reservation management page
|   |   |-- /frontend         # Views for the public-facing site (guests, users)
|   |   |   |-- home.php      # Home page that shows content like blog posts or events
|   |   |   |-- reservation-form.php # Page where users can make reservations
|-- /db                       # Database migrations, seed data, and schema files
|   |-- schema.sql            # SQL file with the database schema
|   |-- migrations            # Folder for migration scripts (e.g., to create tables)
|   |   |-- 2025_01_01_create_reservations_table.php # Migration script for creating reservation table
|   |   |-- 2025_01_02_create_content_table.php     # Migration script for content table
|-- /vendor                   # Third-party libraries and dependencies (composer)
|   |-- /autoload.php         # Composer autoloader
|-- .env                      # Environment variables (DB credentials, API keys, etc.)
|-- .gitignore                # Git ignore file to exclude unnecessary files
|-- composer.json             # Composer dependency manager file
|-- README.md                 # Project documentation (setup, usage, etc.)
