<?php

namespace Tests\Feature;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class JWTAuthControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function register_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson(route('auth.register'), $data);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    }

    #[Test]
    public function login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson(route('auth.login'), $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    #[Test]
    public function get_authenticated_user()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('auth.get-user'));

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
    }

    #[Test]
    public function get_authenticated_user_with_invalid_token()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer invalidtoken'])
            ->getJson(route('auth.get-user'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token is invalid or expired']);
    }

    #[Test]
    public function logout_user()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->post(route('auth.logout'));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    #[Test]
    public function login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson(route('auth.login'), $data);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid credentials']);
    }

    #[Test]
    public function refresh_token_successfully()
    {
        $user = User::factory()->create();
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(route('auth.refresh'));

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    #[Test]
    public function refresh_token_with_invalid_token_fails()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer invalidtoken'])
            ->postJson(route('auth.refresh'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token refresh failed']);
    }

    #[Test]
    public function logout_without_token_fails()
    {
        $response = $this->postJson(route('auth.logout'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token is invalid or expired']);
    }

    #[Test]
    public function access_protected_route_without_token_fails()
    {
        $response = $this->getJson(route('auth.get-user'));

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token is invalid or expired']);
    }

    #[Test]
    public function login_after_logout()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(route('auth.logout'))
            ->assertStatus(200);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }
}
