<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    /** @test */
    public function user_can_login_with_email_and_password()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);

        $this->assertArrayHasKey('token', $response['data']);
    }
}
