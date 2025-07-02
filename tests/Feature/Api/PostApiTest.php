<?php

use App\Models\Post;
use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('can get published posts', function () {
    // Create published and draft posts
    Post::factory()->create([
        'status' => 'published',
        'title' => 'Published Post',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);
    Post::factory()->create([
        'status' => 'draft',
        'title' => 'Draft Post',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/posts');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'content', 'status', 'created_at', 'updated_at']
            ]
        ])
        ->assertJsonCount(1, 'data'); // Only published post should be returned
});

test('can search posts', function () {
    Post::factory()->create([
        'status' => 'published',
        'title' => 'Laravel Tutorial',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);
    Post::factory()->create([
        'status' => 'published',
        'title' => 'PHP Guide',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/posts?search=Laravel');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('can get single post by id', function () {
    $post = Post::factory()->create([
        'status' => 'published',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
            ]
        ]);
});

test('can get single post by slug', function () {
    $post = Post::factory()->create([
        'status' => 'published',
        'slug' => 'test-post',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/posts/test-post');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $post->id,
                'slug' => 'test-post',
            ]
        ]);
});

test('cannot get draft post', function () {
    $post = Post::factory()->create([
        'status' => 'draft',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertStatus(404);
});

test('can create post', function () {
    $postData = [
        'title' => 'New Post',
        'slug' => 'new-post',
        'content' => 'This is the content of the new post.',
        'excerpt' => 'Short excerpt',
        'status' => 'published'
    ];

    $response = $this->postJson('/api/posts', $postData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'title' => 'New Post',
                'slug' => 'new-post',
                'status' => 'published'
            ]
        ]);

    $this->assertDatabaseHas('posts', ['title' => 'New Post', 'slug' => 'new-post']);
});

test('post creation validation', function () {
    $response = $this->postJson('/api/posts', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content', 'slug', 'status']);
});

test('can update post', function () {
    $post = Post::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $updateData = [
        'title' => 'Updated Title',
        'content' => 'Updated content'
    ];

    $response = $this->putJson("/api/posts/{$post->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'title' => 'Updated Title'
            ]
        ]);

    $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated Title']);
});

test('can delete post', function () {
    $post = Post::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->deleteJson("/api/posts/{$post->id}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Post deleted']);

    $this->assertSoftDeleted('posts', ['id' => $post->id]);
});

test('posts include categories', function () {
    $category = Category::factory()->create([
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);
    $post = Post::factory()->create([
        'status' => 'published',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);
    $post->categories()->attach($category->id);

    $response = $this->getJson("/api/posts/{$post->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'categories' => [
                    '*' => ['id', 'name', 'slug']
                ]
            ]
        ]);
});

test('pagination works for posts', function () {
    Post::factory(15)->create([
        'status' => 'published',
        'created_by' => $this->user->id,
        'updated_by' => $this->user->id,
    ]);

    $response = $this->getJson('/api/posts?per_page=5');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ]);
});
