<?php

namespace Boilerplate\Media\Tests\Feature\Controllers\V1\MediaController;

use Boilerplate\Auth\Models\User;
use Boilerplate\Media\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteMedia(): void
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
        $this->deleteJson("/api/v1/media/{$media->getKey()}")
            ->assertOk();

        $this->assertModelMissing($media);
    }
}
