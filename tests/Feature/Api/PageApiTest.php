<?php

use App\Models\Page;
use App\Models\User;

beforeEach(function () {
    // Create a user for foreign key constraints
    User::factory()->create(['id' => 1]);
});

test('can get published pages', function () {
    // Create published and draft pages
    Page::factory()->create(['status' => 'published', 'title' => 'Published Page']);
    Page::factory()->create(['status' => 'draft', 'title' => 'Draft Page']);

    $response = $this->getJson('/api/pages');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'body', 'status', 'created_at', 'updated_at']
            ]
        ])
        ->assertJsonCount(1, 'data'); // Only published page should be returned
});

test('can search pages', function () {
    Page::factory()->create(['status' => 'published', 'title' => 'About Us']);
    Page::factory()->create(['status' => 'published', 'title' => 'Contact']);

    $response = $this->getJson('/api/pages?search=About');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('can get single page by id', function () {
    $page = Page::factory()->create(['status' => 'published']);

    $response = $this->getJson("/api/pages/{$page->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
            ]
        ]);
});

test('can get single page by slug', function () {
    $page = Page::factory()->create(['status' => 'published', 'slug' => 'about-us']);

    $response = $this->getJson('/api/pages/about-us');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $page->id,
                'slug' => 'about-us',
            ]
        ]);
});

test('cannot get draft page', function () {
    $page = Page::factory()->create(['status' => 'draft']);

    $response = $this->getJson("/api/pages/{$page->id}");

    $response->assertStatus(404);
});

test('can create page', function () {
    $pageData = [
        'title' => 'New Page',
        'slug' => 'new-page',
        'body' => 'This is the content of the new page.',
        'status' => 'published'
    ];

    $response = $this->postJson('/api/pages', $pageData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'title' => 'New Page',
                'slug' => 'new-page',
                'status' => 'published'
            ]
        ]);

    $this->assertDatabaseHas('pages', ['title' => 'New Page', 'slug' => 'new-page']);
});

test('page creation validation', function () {
    $response = $this->postJson('/api/pages', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'body', 'slug', 'status']);
});

test('can update page', function () {
    $page = Page::factory()->create();

    $updateData = [
        'title' => 'Updated Page',
        'body' => 'Updated content'
    ];

    $response = $this->putJson("/api/pages/{$page->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'title' => 'Updated Page'
            ]
        ]);

    $this->assertDatabaseHas('pages', ['id' => $page->id, 'title' => 'Updated Page']);
});

test('can delete page', function () {
    $page = Page::factory()->create();

    $response = $this->deleteJson("/api/pages/{$page->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Page deleted']);

    $this->assertSoftDeleted('pages', ['id' => $page->id]);
});

test('pagination works for pages', function () {
    Page::factory(15)->create(['status' => 'published']);

    $response = $this->getJson('/api/pages?per_page=5');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ]);
});
