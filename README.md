# DepEd Zamboanga City Division
## Educational Facility Inventory & GIS Mapping System

A centralized, full-stack geographic information system (GIS) and school registry platform designed to track, manage, and visualize school facilities, resource shortages, and environmental hazard profiles for the **Department of Education – Zamboanga City Division**.

This system was developed as part of a modernization initiative to replace manual administrative workflows with an integrated digital platform featuring interactive maps, automated resource deficit calculations, and official print-ready report generation.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Core Features](#core-features)
- [Prerequisites](#prerequisites)
- [Installation & Setup](#installation--setup)
- [Production Deployment](#production-deployment)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [License](#license)

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | [Laravel](https://laravel.com/) (PHP) |
| **Frontend** | Blade Templating, [Tailwind CSS v4](https://tailwindcss.com/), [Alpine.js](https://alpinejs.dev/), Vanilla JavaScript |
| **Interactive Mapping** | [Leaflet.js](https://leafletjs.com/) — Carto Voyager & Esri Satellite tile layers |
| **Database** | MySQL 8.x |
| **Data Processing** | Native PHP CSV parsing for batch registry imports |
| **Asset Bundling** | [Vite](https://vitejs.dev/) |

---

## Core Features

### 🗺️ Interactive GIS Mapping
Public and administrative map interfaces visualizing school locations across all districts of Zamboanga City. Features dynamic pin color-coding by district, shape indicators for school classification, and toggleable base map layers (street view and satellite imagery).

### 📊 Resource Shortage Engine
Automated calculation of resource deficits across four critical dimensions — **Teachers, Classrooms, Seats, and Toilets** — measured against total enrollment populations per school. Designed to surface actionable data for budget planning and resource prioritization by division administrators.

### ⚠️ Risk & Hazard Profiling
Systematic tracking and map visualization of environmental threats affecting school properties, including **Flood Prone Areas, Landslide Risk Zones, and Seismic Zones**. Enables disaster risk reduction planning at both the school and division level.

### 📥 Batch Import Protocol
Mass upload, synchronization, and conflict-resolution of registry records using standardized CSV templates. Supports initial population of the database and periodic data synchronization with enrollment and facilities reports.

### 🏫 Institutional Masterlist Management
Comprehensive CRUD operations for managing both **public and private school** records, including:
- Utility connectivity status (Power, Water, Internet)
- Curriculum levels and grade configurations
- Geographic coordinates and address metadata
- School classification (Elementary, Secondary, Integrated)

### 🖨️ Official Print Generation
Advanced print-ready CSS formatting that strips web UI elements to instantly produce cleanly formatted, official **DepEd PDF reports** complete with signature blocks, headers, and division branding — suitable for submission and archiving.

---

## Prerequisites

Ensure your server environment meets the following requirements before installation:

| Requirement | Version |
|---|---|
| **PHP** | ^8.1 or higher |
| **Composer** | 2.x |
| **Node.js** | ^16.x or higher |
| **NPM** | Bundled with Node.js |
| **Database** | MySQL 8.x or MariaDB |
| **Web Server** | Nginx or Apache |

---

## Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/your-repo/deped-inventory-system.git
cd deped-inventory-system
```

### 2. Configure Environment Variables

```bash
cp .env.example .env
```

Open the `.env` file and set the following values for your local environment:

```env
APP_NAME="DepEd Zamboanga City Division - GIS System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 3. Install PHP Dependencies

```bash
composer install
```

### 4. Install Node Dependencies & Build Frontend Assets

```bash
npm install
npm run build
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate
```

### 7. (Optional) Seed the Database for Testing

```bash
php artisan db:seed
```

### 8. Link Public Storage

Required for uploaded assets, school logos, and documents to be publicly accessible:

```bash
php artisan storage:link
```

### 9. Start the Local Development Server

```bash
php artisan serve
```

The application will be accessible at `http://localhost:8000` by default.

---

## Production Deployment

### Optimization Commands

Before going live, run the following to cache configuration and optimize autoloading:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
npm run build
```

> **Important:** Point your web server's document root to the `/public` directory of the project. Do not expose the root directory.

### Sample Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/deped-inventory-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Environment Hardening for Production

Update your `.env` file with the following values before deploying:

```env
APP_ENV=production
APP_DEBUG=false
```

Ensure `APP_DEBUG` is always `false` in production to prevent sensitive stack traces from being exposed to end users.

---

## Project Structure

```
deped-inventory-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Application controllers (Schools, Maps, Reports, etc.)
│   │   └── Requests/          # Form request validation classes
│   └── Models/                # Eloquent models (School, District, Hazard, etc.)
├── database/
│   ├── migrations/            # Database schema definitions
│   └── seeders/               # Test and reference data seeders
├── public/                    # Web server document root
├── resources/
│   ├── views/                 # Blade templates
│   │   ├── admin/             # Administrative dashboard views
│   │   ├── maps/              # GIS map interface views
│   │   └── reports/           # Print-ready report templates
│   ├── css/                   # Tailwind CSS source
│   └── js/                    # Alpine.js and Leaflet.js scripts
├── routes/
│   ├── web.php                # Web routes
│   └── api.php                # API routes (if applicable)
├── storage/                   # Uploaded files, logs, caches
├── .env.example               # Environment variable template
└── vite.config.js             # Vite asset bundler configuration
```

---

## Contributing

This system was developed in collaboration with the **DepEd Zamboanga City Division IT team** and participating university interns as part of a local government technology initiative.

For bug reports, feature requests, or institutional feedback:

1. Open an issue on the project repository with a clear description and steps to reproduce.
2. For sensitive institutional matters, contact the development team directly through official DepEd channels.

Please adhere to the project's coding standards and document any new features or schema changes before submitting contributions.

---

## License

This project is **proprietary software** developed exclusively for the **Department of Education – Zamboanga City Division**.

Unauthorized reproduction, distribution, sublicensing, or commercial use of this software or any of its components is strictly prohibited without express written consent from the authors and the DepEd Zamboanga City Division.

© Department of Education – Zamboanga City Division. All rights reserved.
