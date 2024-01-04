<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\UserController;

use Boilerplate\Auth\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('app:acl:sync');
    }

    public function testAdminCanDeleteUser(): void
    {
        $this->artisan('app:acl:sync');

        $user = User::factory()
            ->admin()
            ->create();

        Sanctum::actingAs($user);

        $otherUser = User::factory()
            ->create();

        $this->deleteJson("/api/v1/users/{$otherUser->getKey()}")
            ->assertOk();

        $this->assertModelMissing($otherUser);
    }

    public function testUserCannotDeleteOtherUser(): void
    {
        $this->artisan('app:acl:sync');

        $user = User::factory()
            ->create();

        Sanctum::actingAs($user);

        $otherUser = User::factory()
            ->create();

        $this->deleteJson("/api/v1/users/{$otherUser->getKey()}")
            ->assertForbidden();
    }
}
