<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_email_and_password()
    {
        $this->withoutExceptionHandling();
        $user = User::create([
            'name' => 'new_user',
            'email' => 'new_user@example.net',
            'password' => Hash::make('my-password'),
        ]);

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'my-password',
        ]);

        $response->assertStatus(200);

        $this->assertArrayHasKey('token', $response['data']);
    }

    /** @test */
    public function an_email_is_required()
    {
        $response = $this->post('/api/login', [
            'email' => '',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => 'The email field is required.',
                ]
            ]);
    }

    /** @test */
    public function an_email_is_a_valid_email()
    {
//        $this->withoutExceptionHandling();
        $response = $this->post('/api/login', [
            'email' => 'not_valid_email',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => 'The email must be a valid email address.',
                ]
            ]);
    }

    /** @test */
    public function a_password_is_required()
    {
//        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => $user->email,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => 'The password field is required.',
                ]
            ]);
    }

    /** @test */
    public function password_must_be_a_valid_one()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'secret-123',
        ]);

        $response->assertStatus(402)
            ->assertJson(['Invalid credentials.']);

    }

    /** @test */
    public function email_should_belong_to_a_user()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->post('/api/login', [
            'email' => 'not_a_user_email@example.com',
            'password' => 'secret-123',
        ]);

        $response->assertStatus(402)
            ->assertJson(['Invalid credentials.']);

    }

}
