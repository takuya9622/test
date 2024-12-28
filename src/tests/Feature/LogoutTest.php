<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogout(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('items.index'));

        $this->assertGuest();
    }
}
