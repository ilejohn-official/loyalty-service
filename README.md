# 🚀 Loyalty Program Feature

This project is a **containerized Single Page Application (SPA)** designed to **track and display customer loyalty achievements**.  
It consists of a **Laravel backend API**, a **React frontend**, and a **MySQL database**, all managed via **Docker Compose** for easy setup and consistent deployment.

---

## 💡 Design Choices

### 1. Monorepo with Containerization (Docker)

- **Decoupling:** The frontend (`loyalty-program-fe`) and backend (`loyalty-program`) are separated into distinct services.  
- **Portability:** Using `Docker` and `docker-compose.yml` ensures the entire stack runs consistently across any environment (development or cloud), eliminating *“it works on my machine”* issues.

### 2. Frontend (React/Vite)

- **SPA Structure:** The app uses React to deliver a fast, responsive user experience.  
- **Dynamic Routing:** The customer dashboard is rendered dynamically using React Router URL parameters (`/:userId`), avoiding hardcoding and making the component reusable for any user.  
- **Authentication Guard:** The frontend implements an `AdminGuard` component to protect admin routes (`/admin/dashboard`), ensuring unauthorized users are redirected to the login page.

### 3. Backend (Laravel API)

- **Dedicated Testing Environment:** Includes a separate tester service and database (`loyalty_test`) for isolated, deterministic testing.  
- **Token-Based Mock Security:** Admin routes are secured using a Laravel Middleware that validates a simple Bearer Token against a mock token, enforcing client-side login before granting admin access.

---

## 💻 Project Setup and Execution

To run the entire stack, ensure you have **Docker** and **Docker Compose** installed.

### 1. Clone and Prepare Files

Action

Create a `.env` file inside the **loyalty-program** directory to hold your configuration variables  
(these match the defaults in `docker-compose.yml` but are good practice to include):

```bash
# loyalty-program/.env
DB_DATABASE=loyalty_db
DB_USERNAME=loyalty_user
DB_PASSWORD=secret
DB_ROOT_PASSWORD=root_secret

# Generate a key
php artisan key:generate

APP_KEY=base64:xxx...
```

### 2. Run the Stack

From the root directory (where docker-compose.yml is located), run:

```bash
docker compose up --build
```

This command will:

- Build the necessary Docker images (backend, tester, frontend)

- Start the MySQL container and create loyalty_db

- Start the Laravel API server (Port 8000)

- Start the React development server (Port 5173)

### 3. Accessing the Application

To use the application with prepoulated data, you'll need to run

```bash
docker compose exec backend php artisan db:seed
```

Application URL Credentials / Notes
Customer Dashboard
<http://localhost:5173/[USER_ID>
Use any ID (e.g., /3)
For Prepopulated seeders, you have 1-11 as user ids

Admin Login
<http://localhost:5173/admin/login>
Username: admin / Password: password

### 4. Running Tests

The docker-compose.yml file includes a dedicated tester service configured specifically for running the PHP test suite in isolation.

The tester service:

- Uses APP_ENV=testing

- Connects to a dedicated loyalty_test database

- Stays alive with entrypoint: sleep infinity for interactive testing

Executing the Test Suite

Run all tests with:

```bash
docker compose run --rm --entrypoint sh tester -c "./vendor/bin/pest"
```
