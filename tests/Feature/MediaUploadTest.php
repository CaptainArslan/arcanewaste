<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('uploads media successfully', function () {
    Storage::fake('s3');

    $file = UploadedFile::fake()->image('test.jpg');

    $response = $this->postJson(
        '/api/v1/media/upload',
        [
            'media' => [$file],
        ]
    );

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Media processed successfully.',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'uploaded' => [[
                    'path',
                    'name',
                    'type',
                    'mime_type',
                    'size',
                    'url',
                ]],
                'deleted',
                'failed_deletes',
            ],
        ]);

    // file should exist
    Storage::disk('s3')->assertExists('media/' . $file->hashName());
});

it('returns validation error if no media provided', function () {
    $response = $this->postJson('/api/v1/media/upload', []);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonStructure([
            'success',
            'message',
        ]);
});

it('can delete media files when remove_paths is provided', function () {
    Storage::fake('s3');

    $filePath = 'media/old-file.jpg';
    Storage::disk('s3')->put($filePath, 'dummy content');

    $file = UploadedFile::fake()->image('new.jpg');

    $response = $this->postJson('/api/v1/media/upload', [
        'media' => [$file],
        'remove_paths' => [$filePath],
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Media processed successfully.',
        ]);

    Storage::disk('s3')->assertMissing($filePath);
    Storage::disk('s3')->assertExists('media/' . $file->hashName());
});

it('returns validation error if media is not an array', function () {
    $response = $this->postJson('/api/v1/media/upload', [
        'media' => 'not-an-array',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

it('returns validation error if media contains invalid file', function () {
    $response = $this->postJson('/api/v1/media/upload', [
        'media' => ['not-a-file'],
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});
