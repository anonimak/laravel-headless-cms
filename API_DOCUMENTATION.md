# Laravel Headless CMS API Documentation

## Overview

This is a headless CMS API built with Laravel 11 and the TALL stack, featuring a robust service layer architecture. The API provides RESTful endpoints for managing posts, categories, pages, and media files with full-text search capabilities powered by Laravel Scout.

**Key Features:**
- Service-layer architecture for clean separation of concerns
- Laravel Scout integration for powerful full-text search
- Hierarchical categories with parent-child relationships
- File upload and media management
- Comprehensive validation and error handling
- Resource-based JSON responses
- Soft deletes for data integrity

## Base URL

```
http://your-domain.com/api
```

## Authentication

Currently, this API is public and does not require authentication. In production, you should implement proper authentication mechanisms using Laravel Sanctum or Passport.

## Content Types

All API endpoints accept and return JSON data. Include these headers in your requests:

```
Content-Type: application/json
Accept: application/json
```

## Response Format

All API responses follow Laravel's resource-based JSON structure for consistency.

### Success Response
```json
{
  "data": {
    // Resource data or array of resources
  },
  // Pagination metadata for list endpoints
  "links": {...},
  "meta": {...}
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## Pagination

List endpoints support pagination with these query parameters:
- `per_page`: Number of items per page (default: 10, max: 100)
- `page`: Page number (default: 1)

Paginated responses include metadata:
```json
{
  "data": [...],
  "current_page": 1,
  "per_page": 10,
  "total": 25,
  "last_page": 3,
  "from": 1,
  "to": 10,
  "links": {
    "first": "http://domain.com/api/posts?page=1",
    "last": "http://domain.com/api/posts?page=3",
    "prev": null,
    "next": "http://domain.com/api/posts?page=2"
  }
}
```

## Search

List endpoints support full-text search via the `search` query parameter, powered by Laravel Scout for high-performance searching across content fields.

---

# Posts API

Manage blog posts with categories, images, and publishing workflow.

## List Posts
`GET /api/posts`

Retrieves a paginated list of **published posts only** with their associated categories.

**Query Parameters:**
- `search` (optional): Full-text search across title, excerpt, and content
- `per_page` (optional): Items per page (default: 10, max: 100)
- `page` (optional): Page number (default: 1)

**Example Request:**
```bash
GET /api/posts?search=laravel&per_page=5&page=1
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Getting Started with Laravel 11",
      "slug": "getting-started-with-laravel-11",
      "excerpt": "Learn the fundamentals of Laravel 11 framework with practical examples...",
      "content": "Laravel 11 introduces several new features and improvements...",
      "image": "http://your-domain.com/storage/posts/laravel-11-guide.jpg",
      "status": "published",
      "published_at": "2024-01-15T10:00:00.000000Z",
      "categories": [
        {
          "id": 1,
          "name": "PHP",
          "slug": "php",
          "description": "PHP programming language and frameworks",
          "parent": null,
          "children": [],
          "created_at": "2024-01-10T09:00:00.000000Z",
          "updated_at": "2024-01-10T09:00:00.000000Z"
        },
        {
          "id": 3,
          "name": "Laravel",
          "slug": "laravel",
          "description": "Laravel framework tutorials and tips",
          "parent": {
            "id": 1,
            "name": "PHP",
            "slug": "php"
          },
          "children": [],
          "created_at": "2024-01-10T09:30:00.000000Z",
          "updated_at": "2024-01-10T09:30:00.000000Z"
        }
      ],
      "created_at": "2024-01-15T09:00:00.000000Z",
      "updated_at": "2024-01-15T09:00:00.000000Z"
    }
  ],
  "current_page": 1,
  "per_page": 5,
  "total": 12,
  "last_page": 3,
  "from": 1,
  "to": 5
}
```

## Get Single Post
`GET /api/posts/{id}`

Retrieves a single **published post** by ID or slug with all associated categories.

**Parameters:**
- `id`: Post ID (integer) or slug (string)

**Example Requests:**
```bash
GET /api/posts/1
GET /api/posts/getting-started-with-laravel-11
```

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Getting Started with Laravel 11",
    "slug": "getting-started-with-laravel-11",
    "excerpt": "Learn the fundamentals of Laravel 11 framework...",
    "content": "# Laravel 11 Guide\n\nLaravel 11 introduces...",
    "image": "http://your-domain.com/storage/posts/laravel-11-guide.jpg",
    "status": "published",
    "published_at": "2024-01-15T10:00:00.000000Z",
    "categories": [
      {
        "id": 1,
        "name": "PHP",
        "slug": "php",
        "description": "PHP programming language and frameworks"
      }
    ],
    "created_at": "2024-01-15T09:00:00.000000Z",
    "updated_at": "2024-01-15T09:00:00.000000Z"
  }
}
```

## Create Post
`POST /api/posts`

Creates a new post with automatic slug generation and category associations.

**Request Body:**
```json
{
  "title": "Advanced Laravel Techniques",
  "content": "# Advanced Laravel Techniques\n\nThis post covers advanced Laravel concepts...",
  "excerpt": "Discover advanced Laravel techniques for building robust applications",
  "image": "posts/advanced-laravel.jpg",
  "status": "published",
  "published_at": "2024-01-15T10:00:00Z",
  "category_ids": [1, 3]
}
```

**Validation Rules:**
- `title`: required, string, max 255 characters, must be unique
- `content`: required, string (supports Markdown)
- `excerpt`: optional, string, max 500 characters
- `image`: optional, string, max 255 characters (relative path)
- `status`: required, enum ('draft', 'published', 'archived')
- `published_at`: optional, valid date format
- `category_ids`: optional, array of existing category IDs

**Success Response (201 Created):**
```json
{
  "data": {
    "id": 15,
    "title": "Advanced Laravel Techniques",
    "slug": "advanced-laravel-techniques",
    "excerpt": "Discover advanced Laravel techniques...",
    "content": "# Advanced Laravel Techniques...",
    "image": "http://your-domain.com/storage/posts/advanced-laravel.jpg",
    "status": "published",
    "published_at": "2024-01-15T10:00:00.000000Z",
    "categories": [...],
    "created_at": "2024-01-15T12:00:00.000000Z",
    "updated_at": "2024-01-15T12:00:00.000000Z"
  }
}
```

## Update Post
`PUT /api/posts/{id}`

Updates an existing post. Only provided fields will be updated.

**Request Body:** Same structure as create, all fields optional.

**Example Request:**
```json
{
  "title": "Updated Post Title",
  "status": "draft",
  "category_ids": [2, 4]
}
```

## Delete Post
`DELETE /api/posts/{id}`

Soft deletes a post (preserves data with deleted_at timestamp).

**Success Response (200 OK):**
```json
{
  "message": "Post deleted"
}
```

---

# Categories API

Manage hierarchical categories for organizing content.

## List Categories
`GET /api/categories`

Retrieves a paginated list of categories with optional parent-child relationships.

**Query Parameters:**
- `search` (optional): Search across category names and descriptions
- `per_page` (optional): Items per page (default: 10)
- `page` (optional): Page number (default: 1)
- `with_children` (optional): Include child categories in response (default: false)

**Example Request:**
```bash
GET /api/categories?with_children=true&per_page=20
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Technology",
      "slug": "technology",
      "description": "Technology-related content and tutorials",
      "parent": null,
      "children": [
        {
          "id": 2,
          "name": "Web Development",
          "slug": "web-development",
          "description": "Web development frameworks and tools",
          "parent": {
            "id": 1,
            "name": "Technology",
            "slug": "technology"
          },
          "children": [],
          "created_at": "2024-01-10T09:15:00.000000Z",
          "updated_at": "2024-01-10T09:15:00.000000Z"
        }
      ],
      "created_at": "2024-01-10T09:00:00.000000Z",
      "updated_at": "2024-01-10T09:00:00.000000Z"
    }
  ]
}
```

## Get Single Category
`GET /api/categories/{id}`

Retrieves a single category by ID or slug with parent and children relationships.

**Parameters:**
- `id`: Category ID (integer) or slug (string)

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "name": "Technology",
    "slug": "technology",
    "description": "Technology-related content",
    "parent": null,
    "children": [
      {
        "id": 2,
        "name": "Web Development",
        "slug": "web-development",
        "description": "Web development topics"
      }
    ],
    "created_at": "2024-01-10T09:00:00.000000Z",
    "updated_at": "2024-01-10T09:00:00.000000Z"
  }
}
```

## Create Category
`POST /api/categories`

Creates a new category with automatic slug generation.

**Request Body:**
```json
{
  "name": "Machine Learning",
  "description": "AI and machine learning tutorials and resources",
  "parent_id": 1
}
```

**Validation Rules:**
- `name`: required, string, max 255 characters, must be unique
- `description`: optional, string
- `parent_id`: optional, must exist in categories table, cannot be self-referential

## Update Category
`PUT /api/categories/{id}`

Updates an existing category.

## Delete Category
`DELETE /api/categories/{id}`

Soft deletes a category and all its child categories recursively.

**Note:** Deleting a parent category will also soft delete all its children.

---

# Pages API

Manage static pages with template support.

## List Pages
`GET /api/pages`

Retrieves a paginated list of **published pages only**.

**Query Parameters:**
- `search` (optional): Full-text search across page title and content
- `per_page` (optional): Items per page (default: 10)
- `page` (optional): Page number (default: 1)

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "About Us",
      "slug": "about-us",
      "content": "# About Our Company\n\nWe are a leading technology company...",
      "image": "http://your-domain.com/storage/pages/about-hero.jpg",
      "status": "published",
      "template": "page-with-sidebar",
      "published_at": "2024-01-10T08:00:00.000000Z",
      "created_at": "2024-01-10T07:30:00.000000Z",
      "updated_at": "2024-01-10T07:30:00.000000Z"
    }
  ]
}
```

## Get Single Page
`GET /api/pages/{id}`

Retrieves a single **published page** by ID or slug.

**Parameters:**
- `id`: Page ID (integer) or slug (string)

## Create Page
`POST /api/pages`

Creates a new page with automatic slug generation.

**Request Body:**
```json
{
  "title": "Privacy Policy",
  "content": "# Privacy Policy\n\nThis privacy policy explains...",
  "image": "pages/privacy-banner.jpg",
  "status": "published",
  "template": "legal-page",
  "published_at": "2024-01-15T10:00:00Z"
}
```

**Validation Rules:**
- `title`: required, string, max 255 characters, must be unique
- `content`: required, string (supports Markdown)
- `image`: optional, string, max 255 characters
- `status`: required, enum ('draft', 'published', 'archived')
- `template`: optional, string, max 100 characters
- `published_at`: optional, valid date format

## Update Page
`PUT /api/pages/{id}`

Updates an existing page.

## Delete Page
`DELETE /api/pages/{id}`

Soft deletes a page.

---

# Media API

Manage file uploads and media assets.

## List Media Files
`GET /api/media`

Retrieves a list of all uploaded media files from the storage directory.

**Example Response:**
```json
{
  "data": [
    {
      "name": "hero-image.jpg",
      "url": "http://your-domain.com/storage/media/hero-image.jpg"
    },
    {
      "name": "company-logo.png",
      "url": "http://your-domain.com/storage/media/company-logo.png"
    }
  ]
}
```

## Upload Media File
`POST /api/media`

Uploads a new media file to the storage system.

**Request:** Multipart form data with `file` field.

**Validation Rules:**
- `file`: required, max 2MB, allowed types: jpeg, png, jpg, gif, svg, pdf, doc, docx

**Example Request:**
```bash
curl -X POST http://your-domain.com/api/media \
  -F "file=@/path/to/image.jpg"
```

**Success Response (201 Created):**
```json
{
  "data": {
    "name": "image-1642156789.jpg",
    "url": "http://your-domain.com/storage/media/image-1642156789.jpg"
  }
}
```

## Get Media File Info
`GET /api/media/{filename}`

Retrieves information about a specific media file.

**Parameters:**
- `filename`: Name of the media file

**Example Response:**
```json
{
  "data": {
    "name": "hero-image.jpg",
    "url": "http://your-domain.com/storage/media/hero-image.jpg"
  }
}
```

## Delete Media File
`DELETE /api/media/{filename}`

Permanently deletes a media file from storage.

**Success Response (200 OK):**
```json
{
  "message": "File deleted successfully"
}
```

---

# Error Handling

## HTTP Status Codes

- `200` - OK (Success)
- `201` - Created
- `204` - No Content
- `400` - Bad Request
- `404` - Not Found
- `422` - Unprocessable Entity (Validation Error)
- `500` - Internal Server Error

## Common Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": [
      "The title field is required."
    ],
    "category_ids": [
      "The selected category_ids.0 is invalid."
    ]
  }
}
```

### Not Found Error (404)
```json
{
  "message": "No query results for model [App\\Models\\Post] 999"
}
```

### File Upload Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "file": [
      "The file must be a file of type: jpeg, png, jpg, gif, svg, pdf, doc, docx."
    ]
  }
}
```

---

# Usage Examples

## Complete Blog Post Workflow

### 1. Create Categories
```bash
# Create parent category
curl -X POST http://your-domain.com/api/categories \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Programming",
    "description": "Programming languages and frameworks"
  }'

# Create child category
curl -X POST http://your-domain.com/api/categories \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laravel",
    "description": "Laravel PHP framework tutorials",
    "parent_id": 1
  }'
```

### 2. Upload Featured Image
```bash
curl -X POST http://your-domain.com/api/media \
  -F "file=@/path/to/featured-image.jpg"
```

### 3. Create Blog Post
```bash
curl -X POST http://your-domain.com/api/posts \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Building APIs with Laravel 11",
    "content": "# Building APIs with Laravel 11\n\nLearn how to create robust APIs...",
    "excerpt": "Complete guide to building RESTful APIs with Laravel 11",
    "image": "posts/laravel-api-guide.jpg",
    "status": "published",
    "published_at": "2024-01-15T10:00:00Z",
    "category_ids": [1, 2]
  }'
```

## Search and Filtering

### Search Posts
```bash
# Search published posts
curl "http://your-domain.com/api/posts?search=laravel&per_page=5"

# Get posts with specific categories loaded
curl "http://your-domain.com/api/posts?per_page=20"
```

### Search Categories with Hierarchy
```bash
# Get categories with children
curl "http://your-domain.com/api/categories?with_children=true&search=web"
```

### Search Pages
```bash
# Search published pages
curl "http://your-domain.com/api/pages?search=privacy&per_page=10"
```

## Content Management

### Update Post Status
```bash
curl -X PUT http://your-domain.com/api/posts/1 \
  -H "Content-Type: application/json" \
  -d '{
    "status": "draft"
  }'
```

### Add Categories to Existing Post
```bash
curl -X PUT http://your-domain.com/api/posts/1 \
  -H "Content-Type: application/json" \
  -d '{
    "category_ids": [1, 2, 3]
  }'
```

---

# Best Practices

## API Usage
1. **Always validate responses** - Check HTTP status codes and handle errors appropriately
2. **Use pagination** - Always implement pagination for list endpoints to avoid performance issues  
3. **Implement proper error handling** - Handle network errors, validation errors, and HTTP errors
4. **Cache responses** - Cache GET requests when possible to improve performance
5. **Use slugs for SEO** - Prefer slug-based URLs over IDs for better SEO

## Performance Optimization
1. **Limit per_page** - Don't request more than 100 items per page
2. **Use search efficiently** - Implement debounced search to avoid excessive API calls
3. **Load relationships wisely** - Categories are automatically loaded for posts when needed

## Security Considerations
1. **Validate file uploads** - Always validate file types and sizes on your frontend
2. **Sanitize content** - Sanitize HTML content if allowing rich text input
3. **Implement authentication** - Add proper authentication for production use
4. **Rate limiting** - Respect API rate limits and implement backoff strategies

## Data Integrity
1. **Soft deletes** - Deleted content is preserved with timestamps for data recovery
2. **Relationship integrity** - Category associations are maintained through proper foreign keys
3. **Unique constraints** - Titles and slugs are automatically made unique

---

# Testing

## Test Data
A comprehensive test data seeder script is available at `test_api_data.php` to populate your database with sample content:

```bash
php test_api_data.php
```

This creates:
- Sample categories with hierarchical structure
- Sample posts with various statuses and category associations
- Sample pages with different templates  
- Realistic content for thorough API testing

## Automated Tests
The API includes comprehensive functional tests:

```bash
# Run all API tests
php artisan test --filter=Api

# Run specific endpoint tests
php artisan test tests/Feature/Api/PostApiTest.php
php artisan test tests/Feature/Api/CategoryApiTest.php
php artisan test tests/Feature/Api/PageApiTest.php
php artisan test tests/Feature/Api/MediaApiTest.php
```

---

# Technical Architecture

## Service Layer
The API uses a robust service layer architecture:
- `PostService` - Handles post CRUD operations and Scout search
- `CategoryService` - Manages hierarchical categories and relationships  
- `PageService` - Handles page management with template support
- `MediaManagerService` - Manages file uploads and storage operations

## Laravel Scout Integration
Full-text search is powered by Laravel Scout:
- Automatic indexing of searchable content
- High-performance search across multiple fields
- Configurable search algorithms

## Resource Layer
API responses use Laravel Resource classes for consistent JSON structure:
- `PostResource` - Formats post data with category relationships
- `CategoryResource` - Handles hierarchical category data
- `PageResource` - Formats page data with template information
- `MediaResource` - Provides media file information

This architecture ensures maintainable, testable, and scalable API endpoints with clean separation of concerns.
    "slug": "technology",
    "description": "Tech related posts",
    "parent_id": null
}
```

### Pages
- `GET /pages` - Get all published pages (uses `PageService::getPaginatedPublished()`)
- `GET /pages/{id}` - Get specific page by ID or slug
- `POST /pages` - Create new page
- `PUT /pages/{id}` - Update page
- `DELETE /pages/{id}` - Delete page

#### Create Page Example:
```json
POST /api/pages
{
    "title": "About Us",
    "slug": "about-us",
    "body": "About us content...",
    "status": "published"
}
```

### Media
- `GET /media` - Get all media files
- `GET /media/{filename}` - Get specific media file
- `POST /media` - Upload media file
- `DELETE /media/{filename}` - Delete media file

#### Upload Media Example:
```
POST /api/media
Content-Type: multipart/form-data

file: [binary file data]
```

## Query Parameters

### For List Endpoints:
- `search` - Search term (uses Laravel Scout for full-text search)
- `per_page` - Number of items per page (default: 10)
- `with_children` - Include child categories (categories only)

### Search Examples:
```
GET /api/posts?search=laravel&per_page=5
GET /api/categories?search=technology
GET /api/pages?search=about
```

## Service Layer Integration

This API uses a clean service layer architecture:

- **PostService**: Handles post operations with `getPaginatedPublished()` method
- **CategoryService**: Handles category operations with `getPaginated()` method  
- **PageService**: Handles page operations with `getPaginatedPublished()` method
- **MediaManagerService**: Handles file upload/download operations

### Scout Search Integration

When a search term is provided, the API uses Laravel Scout for full-text search:
- **Posts**: Searches title, slug, and excerpt
- **Categories**: Searches name, slug, and description
- **Pages**: Searches title and slug

## Response Format

All responses are in JSON format with Laravel Resource classes:

```json
{
    "data": [...],
    "links": {
        "first": "...",
        "last": "...",
        "prev": null,
        "next": "..."
    },
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 10,
        "total": 50
    }
}
```

## Relationships

### Post Response includes:
- Categories (when loaded)
- Image URLs (automatically generated)

### Category Response includes:
- Parent category (when exists)
- Child categories (when requested)
- Posts count (when requested)

## Status Codes
- 200: Success
- 201: Created
- 404: Not Found
- 422: Validation Error
- 500: Server Error

## Testing

Run the functional tests with:
```bash
php artisan test tests/Feature/Api/
```

Create test data with:
```bash
php artisan tinker < test_api_data.php
```
