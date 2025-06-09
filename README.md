# ChemLP: Chemistry Learning Platform

Welcome to ChemLP, an interactive chemistry learning platform built as a Single Page Application (SPA) with a RESTful API backend. This project is designed to provide a dynamic learning environment for students and a robust content management tool for administrators.

[Live Deploy](https://chemlp.azurewebsites.net/frontend/views/Login.html) (Error make connection from App to DB [Microsoft Azzure]

## Key Features

### For Administrators

- **Material Management:** Full CRUD (Create, Read, Update, Delete) functionality for learning materials.
- **Content Management:** Add and edit in-depth text content for each material.
- **Quiz Management:**
  - Create and manage quizzes associated with each material.
  - Add, edit, and delete questions for each quiz (currently supporting True/False format).
- **Bulk Material Upload:** A feature to add a complete new material (including its text content, quiz, and questions) by uploading a single CSV file, which is automatically processed and saved to the database in a single transaction.
- **User Management:** View and manage registered users.
- **Secure Authentication:** A secure login system with role-based authorization.

### For Students

- **Centralized Study Page:**
  - A modern sidebar interface for easy navigation.
  - Lists of all available learning materials and quizzes.
  - Displays selected text material content in the main content area.
- **Interactive Quiz Taking:**
  - An interface for taking quizzes with a True/False question format.
  - Navigation between questions (Next/Previous).
  - Auto-advance logic to the next question after an answer is selected.
  - Displays a final quiz result page, including score, percentage, and an answer review.

---

## Architecture & Technology

The project utilizes a modern **Client-Server** model with a clear separation of concerns between the frontend and backend.

### Frontend Architecture (MVC-like)

The frontend is built as a Single Page Application (SPA) and follows a pattern similar to Model-View-Controller (MVC):

- **View:** The pure HTML files located in `frontend/views/` (e.g., `study.html`, `quizManagement.html`). These files define the structure of the user interface.
- **Controller:** The JavaScript files in `frontend/controllers/` (e.g., `StudyController.js`, `QuizManagementController.js`). Each controller manages the logic for a specific view, handles user input, and updates the view.
- **Model:** The JavaScript files in `frontend/services/` (e.g., `QuizService.js`, `MaterialService.js`). This layer is responsible for managing application data and logic, primarily by communicating with the backend REST API via `RestClient.js`.

### Backend Architecture (3-Layer)

The backend RESTful API is structured in three distinct layers to ensure separation of concerns and maintainability:

- **Routes Layer (`routes/`):** This is the entry point for all API requests. It defines the API endpoints (e.g., `POST /materials/upload`) and delegates the handling of requests to the appropriate Service.
- **Service Layer (`services/`):** This layer contains the core business logic of the application. It orchestrates operations, performs data validation, and calls one or more DAOs to interact with the database. For complex operations like the CSV upload, it also manages database transactions.
- **DAO (Data Access Object) Layer (`dao/`):** This layer is responsible for all direct database communication. It contains the raw SQL queries (or query builder logic) to perform CRUD operations on the database tables.

### Technology Stack


| Area          | Technology & Libraries                                                                                       |
| :------------ | :----------------------------------------------------------------------------------------------------------- |
| **Frontend**  | HTML5, CSS3 (Tailwind CSS, Bootstrap 5), JavaScript (ES6+), jQuery, jQuery SPApp, Toastr.js, SweetAlert2     |
| **Backend**   | PHP 8+, FlightPHP (micro-framework),`zircote/swagger-php:^3.3` (API Documentation), JWT (for authentication) |
| **Database**  | MySQL                                                                                                        |
| **Dev Tools** | Composer (PHP dependency manager), Git, Postman (API testing)                                                |

---

## Project Structure

/
├── frontend/
│   ├── css/
│   ├── js/
│   │   ├── custom.js         # Main SPA routing logic
│   │   └── jquery.spapp.js
│   ├── controllers/          # JavaScript controllers per view
│   ├── services/             # JavaScript services for API communication
│   ├── utils/                # Utilities (RestClient.js, auth-nav.js, etc.)
│   └── views/                # HTML templates for the SPA
├── backend/
│   └── rest/
│       ├── config/           # Configuration files (config.php, api_config.php)
│       ├── dao/              # Data Access Objects (BaseDao.php, UserDao.php, etc.)
│       ├── middleware/       # FlightPHP Middleware (RoleMiddleware.php)
│       ├── routes/           # API endpoint definitions per entity
│       ├── services/         # Business logic layer
│       ├── vendor/           # Composer dependencies
│       └── index.php         # Main API entry point
├── .htaccess                 # Apache rewrite rules for the backend
└── README.md                 # You are reading this

---

## Installation & Setup

Follow these steps to set up and run the project in a local development environment.

### Prerequisites

- A local web server (e.g., **XAMPP**, WAMP) with **PHP 8+** and **MySQL**.
- **Composer** installed globally.
- **Git** for cloning the repository.

### How to Install Composer

If you don't have Composer installed, follow these steps:

1. **Windows**: Download and run the official installer from [getcomposer.org/download/](https://getcomposer.org/download/). It will automatically find your PHP installation.
2. **Linux / macOS**: Open your terminal and run the following commands from the official documentation to download and install Composer locally or globally.
   ```bash
   php -r "copy('[https://getcomposer.org/installer](https://getcomposer.org/installer)', 'composer-setup.php');"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   # To make it global, move the composer.phar file
   # sudo mv composer.phar /usr/local/bin/composer
   ```

### 1. Backend Setup

1. **Clone the Repository**

   ```bash
   git clone [https://github.com/YOUR_USERNAME/YOUR_REPOSITORY_NAME.git](https://github.com/YOUR_USERNAME/YOUR_REPOSITORY_NAME.git)
   cd YOUR_REPOSITORY_NAME
   ```
2. **Install PHP Dependencies**
   Navigate to the backend directory and run Composer.

   ```bash
   cd backend/rest
   composer install
   ```

   This command will install FlightPHP, `zircote/swagger-php`, and other dependencies defined in `composer.json`.
3. **Database Setup**

   - Open phpMyAdmin or your preferred database client.
   - Create a new database (e.g., `chemlp1_3`).
   - Import the SQL schema file (`.sql`) to create the necessary tables and populate them with sample data.
4. **Backend Configuration**

   - In `backend/rest/config/`, copy `config.example.php` (if it exists) to a new file named `config.php`.
   - Open `config.php` and update the database connection settings (host, database name, user, password) to match your local setup.
   - Ensure a secret key for JWT is also set in this file.
5. **Web Server Configuration (`.htaccess`)**

   - Ensure `mod_rewrite` is enabled in your Apache configuration.
   - The `.htaccess` file in the `backend/rest/` directory should correctly route all API requests to `index.php`. Example:

   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^ index.php [QSA,L]
   ```

### 2. Frontend Setup

1. **API URL Configuration**
   - Open the file `frontend/utils/constants.js`.
   - Adjust the `PROJECT_BASE_URL` variable to point to the `rest` directory in your backend.

   ```javascript
   // in frontend/utils/constants.js
   const Constants = {
       PROJECT_BASE_URL: "http://localhost/YOUR_PROJECT_FOLDER/backend/rest/"
   };
   ```
2. **Run the Application**
   - Open your browser and navigate to the `Login.html` page.

   ```
   http://localhost/YOUR_PROJECT_FOLDER/frontend/views/Login.html
   ```

---

## API Documentation (Swagger)

API documentation for this project is generated from PHP annotations using `zircote/swagger-php`.

To view the interactive documentation, you will need to point your browser to the endpoint that serves the generated `openapi.json` or `openapi.yaml` file. If configured, this is typically available at a URL like:

`http://localhost/YOUR_PROJECT_FOLDER/backend/rest/docs/` (If using a static Swagger UI setup)

Alternatively, you can generate the documentation file manually by running the `openapi` command from within your `backend/rest/` directory:

```bash
./vendor/bin/openapi . --output docs/openapi.json
```
