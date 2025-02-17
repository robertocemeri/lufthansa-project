<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Url;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;

class UrlControllerTest extends TestCase
{
    use DatabaseTransactions;  

    #[Test]
    public function it_can_shorten_a_url()
    {

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson(route('url.shorten'), [
            'original_url' => 'https://example.com',
        ]);


        $response->assertStatus(201)
            ->assertJsonStructure(['short_url']);
        
        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://example.com',
        ]);
    }

    #[Test]
    public function it_can_return_existing_shortened_url_if_not_expired()
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'short_url' => url('abcdef'),
            'expiry_time' => Carbon::now()->addMinutes(10),
            'access_count' => 0,
        ]);

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson(route('url.shorten'), [
            'original_url' => 'https://example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'short_url' => $url->short_url,
                'is_old' => true,
            ]);
    }

    #[Test]
    public function it_can_resolve_a_valid_short_url()
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'short_url' => url('abcdef'),
            'expiry_time' => Carbon::now()->addMinutes(10),
            'access_count' => 0,
        ]);
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson(route('url.resolve'), ['short_url' => url('abcdef')]);

        $response->assertStatus(201)
            ->assertJson(['original_url' => 'https://example.com']);

        $this->assertDatabaseHas('urls', [
            'short_url' => url('abcdef'),
            'access_count' => 1, // Ensure access count increased
        ]);
    }

    #[Test]
    public function it_returns_404_when_resolving_non_existent_url()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson(route('url.resolve'), ['short_url' => url('nonexistent')]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'URL not found']);
    }

    #[Test]
    public function it_returns_410_when_url_has_expired()
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'short_url' => url('expired123'),
            'expiry_time' => Carbon::now()->subMinutes(10),
            'access_count' => 0,
        ]);

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->postJson(route('url.resolve'), ['short_url' => url('expired123')]);

        $response->assertStatus(410)
            ->assertJson(['message' => 'URL has expired']);
    }

    #[Test]
    public function it_can_update_url_expiry_time()
    {
        $url = Url::create([
            'original_url' => 'https://example.com',
            'short_url' => url('update123'),
            'expiry_time' => Carbon::now()->addMinutes(10),
            'access_count' => 0,
        ]);

        $newExpiryTime = 30; // Extend by 30 minutes

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->putJson(route('url.update-expiry-time'), [
            'short_url' => url('update123'),
            'expiry_time' => $newExpiryTime,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Expiration time updated']);

        $this->assertDatabaseHas('urls', [
            'short_url' => url('update123'),
        ]);
    }

    #[Test]
    public function it_returns_404_when_updating_non_existent_url()
    {
        $response =  $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->putJson(route('url.update-expiry-time'), [
            'short_url' => 'nonexistent',
            'expiry_time' => 30,
        ]);

        $response->assertStatus(404)
            ->assertJson(['message' => 'URL not found']);
    }
}
