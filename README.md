# Laravel Headless CMS

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
    <strong>A modern headless CMS built with Laravel 11 and the TALL stack</strong>
</p>

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-11.x-red.svg" alt="Laravel 11">
    <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP 8.2+">
    <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="MIT License">
</p>

## About This Project

Laravel Headless CMS is a powerful, API-first content management system built with Laravel 11. It provides a clean, RESTful API for managing content that can be consumed by any frontend application—whether it's a React SPA, Vue.js application, mobile app, or static site generator.

### Key Features

- 🚀 **RESTful API** - Clean, well-documented API endpoints
- 🔍 **Full-Text Search** - Powered by Laravel Scout for lightning-fast content search
- 📱 **Headless Architecture** - Use any frontend technology
- 🏗️ **Service Layer** - Clean architecture with separation of concerns
- 📂 **Hierarchical Categories** - Nested category structure for content organization
- 📄 **Content Management** - Posts, pages, categories, and media management
- 🔐 **Validation** - Comprehensive input validation and error handling
- 📊 **Pagination** - Efficient data loading with customizable pagination
- 🗃️ **Soft Deletes** - Data preservation with recovery capabilities
- 🧪 **Fully Tested** - Comprehensive test suite with PHPUnit/Pest

### Tech Stack

- **Backend**: Laravel 11 with PHP 8.2+
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **Search**: Laravel Scout (configurable drivers)
- **Testing**: PHPUnit/Pest for comprehensive test coverage
- **Architecture**: Service layer pattern with API resources

## Quick Start

### Prerequisites

Make sure you have the following installed:
- PHP 8.2 or higher
- Composer
- Node.js & NPM (for asset compilation)
- SQLite (included) or MySQL/PostgreSQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/anonimak/laravel-headless-cms
   cd laravel-headless-cms
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # Create SQLite database (default)
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate
   
   # Seed with sample data (optional)
   php artisan db:seed
   ```

6. **Configure Laravel Scout** (for search functionality)
   ```bash
   # Publish Scout configuration
   php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
   
   # Index existing content
   php artisan scout:import "App\Models\Post"
   php artisan scout:import "App\Models\Page"
   php artisan scout:import "App\Models\Category"
   ```

7. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

Your API will be available at `http://localhost:8000/api`

## API Documentation

Comprehensive API documentation is available in [`API_DOCUMENTATION.md`](API_DOCUMENTATION.md). The API provides endpoints for:

- **Posts** - Create, read, update, delete blog posts with categories
- **Categories** - Hierarchical category management
- **Pages** - Static page management with templates
- **Media** - File upload and media management

### Quick API Examples

```bash
# Get all published posts
curl http://localhost:8000/api/posts

# Search posts
curl "http://localhost:8000/api/posts?search=laravel&per_page=5"

# Get categories with children
curl "http://localhost:8000/api/categories?with_children=true"

# Create a new post
curl -X POST http://localhost:8000/api/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "My First Post",
    "content": "Post content here...",
    "status": "published"
  }'
```

## Development

### Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/    # API controllers
│   ├── Requests/Api/       # Form request validation
│   └── Resources/Api/      # API resource transformers
├── Models/                 # Eloquent models
├── Services/              # Business logic layer
└── ...
routes/
├── api.php               # API routes
└── ...
tests/
├── Feature/Api/          # API integration tests
└── Unit/                 # Unit tests
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run API tests only
php artisan test --filter=Api

# Run specific test file
php artisan test tests/Feature/Api/PostApiTest.php

# Run tests with coverage
php artisan test --coverage
```

## Configuration

### Environment Variables

Key environment variables to configure:

```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# Scout Search Driver
SCOUT_DRIVER=collection  # or 'algolia', 'meilisearch', etc.

# File Storage
FILESYSTEM_DISK=public

# App
APP_URL=http://localhost:8000
```

### Search Configuration

The project uses Laravel Scout for search. Configure your preferred search driver in `config/scout.php`:

- **Collection Driver** (default) - Good for development and small datasets
- **Database Driver** - MySQL/PostgreSQL full-text search
- **Algolia** - Cloud search service
- **Meilisearch** - Self-hosted search engine

## Deployment

### Production Setup

1. **Environment Configuration**
   ```bash
   # Set production environment
   APP_ENV=production
   APP_DEBUG=false
   
   # Configure database
   DB_CONNECTION=mysql
   DB_HOST=your-db-host
   DB_DATABASE=your-db-name
   DB_USERNAME=your-db-user
   DB_PASSWORD=your-db-password
   ```

2. **Optimization**
   ```bash
   # Cache configuration
   php artisan config:cache
   
   # Cache routes
   php artisan route:cache
   
   # Cache views
   php artisan view:cache
   
   # Optimize autoloader
   composer install --optimize-autoloader --no-dev
   ```

3. **Security**
   - Set up proper file permissions
   - Configure HTTPS
   - Set up CORS for your frontend domain
   - Implement rate limiting
   - Add authentication (Laravel Sanctum recommended)


## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request


<p align="center">Built with ❤️ using Laravel 11</p>
