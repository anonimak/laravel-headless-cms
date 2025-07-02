<?php

use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('can get categories', function () {
    Category::factory(3)->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'slug', 'description', 'parent_id', 'created_at', 'updated_at']
            ]
        ])
        ->assertJsonCount(3, 'data');
});

test('can search categories', function () {
    Category::factory()->create([
        'name' => 'Technology',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);
    Category::factory()->create([
        'name' => 'Sports',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/categories?search=Technology');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('can get single category by id', function () {
    $category = Category::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson("/api/categories/{$category->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]
        ]);
});

test('can get single category by slug', function () {
    $category = Category::factory()->create([
        'slug' => 'test-category',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/categories/test-category');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $category->id,
                'slug' => 'test-category',
            ]
        ]);
});

test('can create category', function () {
    $categoryData = [
        'name' => 'New Category',
        'slug' => 'new-category',
        'description' => 'This is a new category.',
    ];

    $response = $this->postJson('/api/categories', $categoryData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => 'New Category',
                'slug' => 'new-category',
            ]
        ]);

    $this->assertDatabaseHas('categories', ['name' => 'New Category', 'slug' => 'new-category']);
});

test('can create category with parent', function () {
    $parent = Category::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $categoryData = [
        'name' => 'Child Category',
        'slug' => 'child-category',
        'parent_id' => $parent->id,
    ];

    $response = $this->postJson('/api/categories', $categoryData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => 'Child Category',
                'parent_id' => $parent->id,
            ]
        ]);
});

test('category creation validation', function () {
    $response = $this->postJson('/api/categories', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'slug']);
});

test('can update category', function () {
    $category = Category::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $updateData = [
        'name' => 'Updated Category',
        'description' => 'Updated description'
    ];

    $response = $this->putJson("/api/categories/{$category->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'name' => 'Updated Category'
            ]
        ]);

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Updated Category']);
});

test('can delete category', function () {
    $category = Category::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->deleteJson("/api/categories/{$category->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Category deleted']);

    $this->assertSoftDeleted('categories', ['id' => $category->id]);
});

test('categories include parent and children', function () {
    $parent = Category::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);
    $child = Category::factory()->create([
        'parent_id' => $parent->id,
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson("/api/categories/{$parent->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'parent',
                'children' => [
                    '*' => ['id', 'name', 'slug']
                ]
            ]
        ]);
});

test('pagination works for categories', function () {
    Category::factory(15)->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/categories?per_page=5');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ]);
});
