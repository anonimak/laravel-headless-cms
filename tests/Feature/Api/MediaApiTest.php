<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('can get media files', function () {
    // Mock storage to avoid actual file operations in tests
    Storage::fake('public');

    // Create some fake files
    Storage::disk('public')->put('media/test1.jpg', 'fake content');
    Storage::disk('public')->put('media/test2.png', 'fake content');

    $response = $this->getJson('/api/media');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['name', 'url']
            ]
        ]);
});

test('can upload media file', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson('/api/media', [
        'file' => $file
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['name', 'url']
        ]);

    // Check if file was stored
    expect(Storage::disk('public')->exists('media/' . $file->hashName()))->toBeTrue();
});

test('can upload different file types', function () {
    Storage::fake('public');

    $imageFile = UploadedFile::fake()->image('test.jpg');

    // png file
    $pngFile = UploadedFile::fake()->image('test.png');

    // Test image upload
    $response = $this->postJson('/api/media', ['file' => $imageFile]);
    $response->assertStatus(200);

    // Test PNG upload
    $response = $this->postJson('/api/media', ['file' => $pngFile]);
    $response->assertStatus(200);
});

test('media upload validation', function () {
    $response = $this->postJson('/api/media', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['file']);
});

test('media upload file size validation', function () {
    Storage::fake('public');

    // Create a file larger than 10MB (10240 KB)
    $largeFile = UploadedFile::fake()->create('large.jpg', 11000);

    $response = $this->postJson('/api/media', [
        'file' => $largeFile
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['file']);
});

test('can get single media file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/test.jpg', 'fake content');

    $response = $this->getJson('/api/media/test.jpg');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'name' => 'test.jpg'
            ]
        ])
        ->assertJsonStructure([
            'data' => ['name', 'url']
        ]);
});

test('returns 404 for non-existent media file', function () {
    Storage::fake('public');

    $response = $this->getJson('/api/media/nonexistent.jpg');

    $response->assertStatus(404)
        ->assertJson(['message' => 'File not found']);
});

test('can delete media file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/test.jpg', 'fake content');

    $response = $this->deleteJson('/api/media/test.jpg');

    $response->assertStatus(200)
        ->assertJson(['message' => 'File deleted']);

    expect(Storage::disk('public')->exists('media/test.jpg'))->toBeFalse();
});

test('uploaded files have correct url structure', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson('/api/media', ['file' => $file]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['name', 'url']
        ]);

    $responseData = $response->json('data');
    expect($responseData['url'])->toContain('storage/media/');
    expect($responseData['name'])->toMatch('/\.(jpg|jpeg|png|gif)$/i');
});
