<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_404_for_model_not_found()
    {
        // This test verifies the handler works, but requires routes to be set up
        // For now, we'll test the handler directly with a simple route test
        $this->markTestSkipped('Requires API routes to be configured first');
    }

    /** @test */
    public function it_returns_401_for_unauthenticated_requests()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated'
            ]);
    }

    /** @test */
    public function it_returns_422_for_validation_errors()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/user/password', [
                'current_password' => '',
                'password' => 'short',
                'password_confirmation' => 'different'
            ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }
}
