<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_added_by_registration()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmed' => 'super-secret',
        ]);

        $response->assertOk();
        $this->assertCount(1, User::all());
    }
}
