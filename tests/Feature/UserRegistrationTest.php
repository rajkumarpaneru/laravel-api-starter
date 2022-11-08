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
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $response->assertOk();
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function a_name_is_required()
    {
        $response = $this->post('/api/register', [
            'name' => '',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $response->assertSessionHasErrors();

    }

    /** @test */
    public function an_email_is_required()
    {
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => '',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function an_email_is_a_valid_email()
    {
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user.com',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function an_email_is_unique_among_users()
    {
        $this->post('/api/register', [
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

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function a_password_is_required()
    {
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => '',
            'password_confirmation' => 'super-secret',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function a_password_is_at_least_eight_character_long()
    {
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super',
            'password_confirmation' => 'super',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function a_password_must_be_confirmed()
    {
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function hashed_password_is_stored_in_database()
    {
        $response = $this->post('/api/register', [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ]);

        $hash = Hash::check('super-secret', User::first()->password);
        $this->assertEquals( true, $hash);
    }

}
