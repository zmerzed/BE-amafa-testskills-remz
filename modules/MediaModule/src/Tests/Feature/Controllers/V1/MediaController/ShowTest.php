<?php

namespace Boilerplate\Media\Tests\Feature\Controllers\V1\MediaController;

use Boilerplate\Auth\Models\User;
use Boilerplate\Media\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function testGetMedia(): void
    {
        $user = User::factory()
            ->create();

        $media = Media::factory()
            ->create(
                [
                    'model_type' => User::class,
                    'model_id' => $user->getKey(),
                ]
            );

        $this->actingAs($user);
        $this->getJson("/api/v1/media/{$media->getKey()}")
            ->assertOk()
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
                    'data' => array_merge(
                        $media->only(
                            [
                                'id',
                                'name',
                                'file_name',
                                'collection_name',
                                'mime_type',
                                'size',
                            ]
                        )
                    ),
                ]
            );
    }

    public function testUserCannotGetUnassignedMediaOfOtherUsers(): void
    {
        $user = User::factory()
            ->create();

        $media = Media::factory()
            ->create(
                [
                    'model_type' => User::class,
                    'model_id' => User::factory()->create()->getKey(),
                ]
            );

        $this->actingAs($user);
        $this->getJson("/api/v1/media/{$media->getKey()}")
            ->assertForbidden();
    }
}
