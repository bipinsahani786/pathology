# Production Deployment Guide

This document outlines the process for deploying the application to a production server using Docker and Docker Compose.

## Prerequisites

Ensure your server meets the following requirements:

- **Docker**: Installed and running.
- **Docker Compose**: Installed (usually comes with Docker Desktop or can be installed separately).
- **Git**: Installed (for pulling code).
- **Bash**: A Unix-like shell environment to run the deployment script.

## Deployment Architecture

The production setup runs on the following containers:

- **App (`app`)**: Runs PHP 8.2 FPM. This image is built in multi-stage, containing only production dependencies (Composer & NPM build artifacts).
- **Web (`web`)**: Runs Nginx Alpine to serve static assets and proxy PHP requests to the `app` container.
- **Database (`dbpostgres`)**: Runs PostgreSQL 15.
- **Cache (`redis`)**: Runs Redis 7 for caching and session management.

## Setup Steps

### 1. Clone the Repository

Clone the project repository to your production server:

```bash
git clone <your-repository-url> /var/www/pathology
cd /var/www/pathology
```

### 2. Configure Environment Variables

Copy the example environment file and configure it for production:

```bash
cp .env.example .env
```

Open the `.env` file and make sure to configure at least the following correctly:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- Database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- Redis credentials if any.

### 3. Make Deployment Script Executable

Ensure the deployment script has execute permissions:

```bash
chmod +x deploy.sh
```

### 4. Deploy the Application

Run the deployment script:

```bash
./deploy.sh
```

## What the Deployment Script (`deploy.sh`) Does:

1. **Builds Docker Images**: It reads `docker-compose.prod.yml` and builds the `Dockerfile.prod`.
    - **Stage 1**: Installs PHP dependencies securely (`composer install --no-dev`).
    - **Stage 2**: Installs Node dependencies and builds frontend assets (`npm run build`).
    - **Stage 3**: Combines both and prepares the final PHP-FPM production image.
2. **Starts Containers**: Boots up the application (`app`, `web`, `dbpostgres`, `redis`) in detached mode.
3. **Database Migrations**: Runs Laravel database migrations securely (`php artisan migrate --force`).
4. **Caches Resources**: Optimizes Laravel performance by caching config, routes, views, and events.
5. **Permissions Check**: Fixes required host-to-container permissions for Laravel `storage` and `bootstrap/cache` folders.

## Managing the Deployment

- **Viewing logs**:
    ```bash
    docker compose -f docker-compose.prod.yml logs -f
    ```
- **Stopping the application**:
    ```bash
    docker compose -f docker-compose.prod.yml down
    ```
- **Running Artisan Commands**:
    ```bash
    docker compose -f docker-compose.prod.yml exec app php artisan <command>
    ```

## SSL / HTTPS Configuration (Optional)

This setup serves HTTP traffic over port `80`. For production, it's highly recommended to use **HTTPS**.
You can use **Let's Encrypt** with Nginx, or put a reverse proxy like **Traefik** or **Cloudflare** in front of the server to handle SSL termination. If you update the `nginx` configuration, place your certs in the server and map them via volumes in `docker-compose.prod.yml`.
