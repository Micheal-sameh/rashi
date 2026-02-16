<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RefreshTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_obtain_and_use_refresh_token()
    {
        // create a user and generate a refresh token via service
        $user = User::factory()->create();
        $service = app(RefreshTokenService::class);

        $refreshString = $service->createForUser($user);
        $this->assertIsString($refreshString);

        // hitting the API without logging in should work using refresh token
        $response = $this->postJson('/api/refresh', [
            'refresh_token' => $refreshString,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['token', 'refresh_token', 'user'],
        ]);

        $newRefresh = $response->json('data.refresh_token');
        $this->assertNotEquals($refreshString, $newRefresh, 'token should rotate');

        // old token should now be revoked in DB
        $old = $service->findByPlain($refreshString);
        $this->assertTrue($old->isRevoked());
    }

    public function test_refresh_fails_with_invalid_token()
    {
        $response = $this->postJson('/api/refresh', [
            'refresh_token' => 'not-a-valid-token',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout_revokes_only_device_tokens()
    {
        $user = User::factory()->create();
        $service = app(RefreshTokenService::class);

        // two devices
        $tokenA = $service->createForUser($user, 'android', 'imeiA');
        $tokenB = $service->createForUser($user, 'android', 'imeiB');

        // ensure both exist in db
        $this->assertNotNull($service->findByPlain($tokenA));
        $this->assertNotNull($service->findByPlain($tokenB));

        // simulate logout from device A (need authentication)
        $this->actingAs($user, 'sanctum');
        $response = $this->postJson('/api/logout', [
            'device_type' => 'android',
            'imei' => 'imeiA',
        ]);

        $response->assertStatus(200);

        // A revoked, B still present
        $this->assertTrue($service->findByPlain($tokenA)->isRevoked());
        $this->assertFalse($service->findByPlain($tokenB)->isRevoked());
    }
}
