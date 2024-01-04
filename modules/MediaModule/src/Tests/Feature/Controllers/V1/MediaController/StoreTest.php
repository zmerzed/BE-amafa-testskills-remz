<?php

namespace Boilerplate\Media\Tests\Feature\Controllers\V1\MediaController;

use Boilerplate\Auth\Models\User;
use Boilerplate\Media\Enums\MediaCollectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function testUploadMedia(): void
    {
        $user = User::factory()
            ->create();

        $this->actingAs($user);

        $this->postJson('/api/v1/media', ['file' => UploadedFile::fake()->image('test.png')])
            ->assertCreated()
            ->assertJsonStructure(
                [
                    'data' => [
                        'id',
                        'name',
                        'file_name',
                        'collection_name',
                        'mime_type',
                        'size',
                        'created_at',
                        'url',
                        'thumb_url',
                        'responsive_url',
                    ],
                ]
            )
            ->assertJson(
                [
                    'data' => [
                        'name' => 'test',
                        'file_name' => 'test.png',
                    ],
                ]
            );

        $this->assertDatabaseHas(
            'media',
            [
                'model_type' => User::class,
                'model_id' => $user->getKey(),
                'collection_name' => MediaCollectionType::UNASSIGNED,
                'name' => 'test',
                'file_name' => 'test.png',
                'mime_type' => 'image/png',
            ]
        );
    }
}
