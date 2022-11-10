<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_added_by_registration()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $this->assertCount(1, User::all());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'first_user',
                    'email' => 'first_user@example.net',
                ]
            ]);
    }

    /** @test */
    public function a_name_is_required()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $this->assertCount(0, User::all());

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => 'The name field is required.',
                ]
            ]);

    }

    /** @test */
    public function an_email_is_required()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => '',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $this->assertCount(0, User::all());

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
        $response = $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user.com',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);
        $this->assertCount(0, User::all());

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => 'The email must be a valid email address.',
                ]
            ]);
    }

    /** @test */
    public function an_email_is_unique_among_users()
    {
        $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $response = $this->post('/api/register', [
            'name' => 'second_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => 'The email has already been taken.',
                ]
            ]);
    }

    /** @test */
    public function a_password_is_required()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => '',
            'password_confirmation' => 'super-secret',
        ]);

        $this->assertCount(0, User::all());

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => 'The password field is required.',
                ]
            ]);
    }

    /** @test */
    public function a_password_is_at_least_eight_character_long()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super',
            'password_confirmation' => 'super',
        ]);

        $this->assertCount(0, User::all());

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => 'The password must be at least 8 characters.',
                ]
            ]);
    }

    /** @test */
    public function a_password_must_be_confirmed()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super',
        ]);

        $this->assertCount(0, User::all());

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => 'The password confirmation does not match.',
                ]
            ]);
    }

    /** @test */
    public function hashed_password_is_stored_in_database()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $hash = Hash::check('super-secret', User::first()->password);
        $this->assertEquals(true, $hash);
    }

}
