# Infrastructure & Development Setup

The setup of this project is optimized using raw **Docker** via the `docker-compose.yml` in the root — ensuring total freedom of control without relying on wrappers like Laravel Sail.

## How to Run the Project in DEV (Step-by-step)

1. **Clone the environment to your machine:**
   ```bash
   git clone <REPOSITORY_URL>
   cd multilead
   ```

2. **Separate and inject the appropriate variables:**
   ```bash
   cp .env.example .env
   ```

3. **Start the services via Docker Compose:**
   This will spin up all vital servers in their designated containers (Nginx, Alpine/PHP, PostgreSQL, Node) on the local network.
   ```bash
   docker-compose up -d
   ```

4. **Work via SSH in the Containers:**
   Use the Alpine *shell* (`ash`) to primarily enter the main application host where we will perform command-line script actions:
   ```bash
   docker exec -it multilead ash
   ```

5. **Still _inside_ the container (`multilead`), run the library installation:**
   ```bash
   composer install
   npm install
   ```

6. **Generating and Initializing the database (Migrations & Seeds):**
   With dependencies in place, prepare Laravel.
   ```bash
   php artisan key:generate
   ```
   Run in one go the reconstruction of relative tables and inject all populated trees for the dev environments:
   ```bash
   php artisan migrate --seed
   ```

7. **Compile the Front-End in parallel (Vite):**
   ```bash
   npm run build
   ```

Done! The project is visible in the local development environment at the Nginx declared URL: `http://localhost`.

---

## Code Quality & Standardization Maintenance (Pint)
If you make changes/pull requests to `.php` files, ensure the formatter has rewritten any misadjusted snippets by running (inside the container):
```bash
./vendor/bin/pint
```