<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginFailsWhenMailIsEmpty(): void
    {
        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $response = $this->from(route('login'))
        ->post(route('login', $data));

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('login'));

        $response = $this->get(route('login'));
        $response->assertSeeText('メールアドレスを入力してください');
    }

    public function testLoginFailsWhenPasswordIsEmpty(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $response = $this->from(route('login'))
        ->post(route('login', $data));

        $response->assertSessionHasErrors(['password']);

        $response->assertRedirect(route('login'));

        $response = $this->get(route('login'));
        $response->assertSeeText('パスワードを入力してください');
    }

    public function testLoginFailsWhenCredentialsAreInvalid(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'password' => bcrypt('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password456',
        ];

        $response = $this->from(route('login'))
        ->post(route('login', $data));

        $response->assertSessionHasErrors(['email']);

        $response->assertRedirect(route('login'));

        $response = $this->get(route('login'));
        $response->assertSeeText('ログイン情報が登録されていません');
    }

    public function testUserCanLogin(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->post(route('login', $data));

        $response->assertRedirect(route('items.index'));

        $this->assertAuthenticatedAs($user);

        $response = $this->get(route('items.index'));
        $response->assertStatus(200);
    }
}
