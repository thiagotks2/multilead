# Infrastructure & Development Setup

The development environment is fully containerized using a custom **Docker** stack (orchestrated via `docker-compose.yml`). We intentionally avoid abstract wrappers like *Laravel Sail* to maintain granular control over the services (Nginx, PHP-FPM 8.5, PostgreSQL 15, and Node.js).

---

## Quick Start (Development Setup)

Follow these steps to spin up the local environment from scratch:

### 1. Clone the Repository
Clone the project and enter the directory:
```bash
git clone git@github.com:thiagotks2/multilead.git
cd multilead
```

### 2. Environment Configuration
Initialize the environment variables before building the containers:
```bash
cp .env.example .env
```
> [!NOTE]
> Ensure the DB credentials in your `.env` match the defaults in `docker-compose.yml` (Pre-configured for local dev: `postgres`, `multilead`, `multilead0000`).

### 3. Build and Start the Containers
This command builds the application image and starts all necessary services (Web, DB, Network) in the background:
```bash
docker-compose up -d --build
```

### 4. Interactive Shell (Entering the Container)
The application runs on a lightweight **Alpine Linux** image. To run Artisan, Composer, or NPM commands, enter the `multilead` container shell:
```bash
docker-compose exec multilead ash
```

### 5. Install Dependencies (Inside the Container)
Once inside the container shell, install PHP and JavaScript dependencies:
```bash
composer install
npm install
```

### 6. Application Initialization
Complete the setup by generating the app key and preparing the database:
```bash
php artisan key:generate
php artisan migrate --seed
```
The `--seed` flag will populate the database with a realistic multi-tenant hierarchy (Tenants, Companies, Branches, and Modules).

### 7. Compile Assets
For a local "production-like" feel or to see reflect changes:
```bash
npm run build
```

---

## Verification & Access

Once initialization is complete, you can access the two distinct Filament panels using the default development credentials.

### Admin Panel (System Administration)
Used for platform-wide configurations and managing tenants.
- **URL:** `http://localhost/admin`
- **Login:** `admin@admin.com`
- **Password:** `123`

### App Panel (Tenant/Real Estate Operations)
Used for day-to-day operations (Leads, Properties, Site Configs).
- **URL:** `http://localhost/app`
- **Login:** `user@user.com`
- **Password:** `123`
> [!NOTE]
> The App panel is multi-tenant. Upon login, you will be automatically redirected to the respective company's dashboard (e.g., `http://localhost/app/1`).

### Database Access
Accessible via any GUI (DBeaver, TablePlus, etc.) using the credentials defined in your `.env`.
- **Host:** `localhost`
- **Port:** `5432`
- **User/DB/Pass:** Defaulted to `multilead`/`multilead`/`multilead0000`

---

## Code Quality Standardization (Pint)
This project enforces **PSR-12** standards strictly. Before committing any PHP code, run the following command inside the container to ensure formatting compliance:
```bash
./vendor/bin/pint --dirty
```