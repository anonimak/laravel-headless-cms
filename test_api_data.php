<?php

// Simple API test script
// Run with: php artisan tinker < test_api.php

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Page;

echo "Creating test data...\n";

// Create a user
$user = User::factory()->create([
    'name' => 'Test User',
    'email' => 'test@example.com'
]);

// Create categories
$category1 = Category::factory()->create([
    'name' => 'Technology',
    'slug' => 'technology',
    'created_by' => $user->id,
    'updated_by' => $user->id,
]);

$category2 = Category::factory()->create([
    'name' => 'Laravel',
    'slug' => 'laravel',
    'created_by' => $user->id,
    'updated_by' => $user->id,
]);

// Create posts
$post1 = Post::factory()->create([
    'title' => 'Laravel API Tutorial',
    'slug' => 'laravel-api-tutorial',
    'content' => 'This is a tutorial about Laravel APIs...',
    'status' => 'published',
    'created_by' => $user->id,
    'updated_by' => $user->id,
]);

$post2 = Post::factory()->create([
    'title' => 'Draft Post',
    'slug' => 'draft-post',
    'content' => 'This is a draft post...',
    'status' => 'draft',
    'created_by' => $user->id,
    'updated_by' => $user->id,
]);

// Attach categories to posts
$post1->categories()->attach([$category1->id, $category2->id]);

// Create pages
$page = Page::factory()->create([
    'title' => 'About Us',
    'slug' => 'about-us',
    'body' => 'This is our about page...',
    'status' => 'published',
    'created_by' => $user->id,
    'updated_by' => $user->id,
]);

echo "Test data created successfully!\n";
echo "You can now test the API endpoints:\n";
echo "GET /api/posts - Get published posts\n";
echo "GET /api/categories - Get categories\n";
echo "GET /api/pages - Get published pages\n";
echo "GET /api/posts?search=laravel - Search posts\n";
