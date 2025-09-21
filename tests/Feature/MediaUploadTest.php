<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads media successfully', function () {
    // Fake the S3 storage
    Storage::fake('s3');

    // Create a fake file
    $file = UploadedFile::fake()->image('test.jpg');

    // Call the API
    $response = $this->postJson('/api/v1/media/upload', [
        'media' => [$file],
    ]);

    // Assertions
    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Media processed successfully.',
        ]);

    // Check the file exists in fake storage
    Storage::disk('s3')->assertExists('media/' . $file->hashName());
});

it('returns validation error if no media provided', function () {
    $response = $this->postJson('/api/v1/media/upload', []);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

it('can delete media files when remove_paths is provided', function () {
    Storage::fake('s3');

    // Put a fake file into storage first
    $filePath = 'media/old-file.jpg';
    Storage::disk('s3')->put($filePath, 'dummy content');

    // Upload a new file while asking to delete the old one
    $file = UploadedFile::fake()->image('new.jpg');

    $response = $this->postJson('/api/v1/media/upload', [
        'media' => [$file],
        'remove_paths' => [$filePath],
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
        ]);

    // Old file should be deleted
    Storage::disk('s3')->assertMissing($filePath);

    // New file should exist
    Storage::disk('s3')->assertExists('media/' . $file->hashName());
});

it('returns validation error if media is not an array', function () {
    $response = $this->postJson('/api/v1/media/upload', [
        'media' => 'not an array',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

it('returns validation error if media is not a file', function () {
    $response = $this->postJson('/api/v1/media/upload', [
        'media' => 'not a file',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});