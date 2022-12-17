<?php

namespace Tests\Feature;

use App\Mail\VerifyEmailMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_added_by_registration()
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        $response = $this->postJson('/api/register', $this->getData());

        // Assert a message was sent to given email addresses
        Mail::assertQueued(VerifyEmailMail::class, function ($mail) use ($request) {
            return $mail->hasTo($request['email']) &&
                $mail->hasFrom(config('mail.from.address'));
        });

        $this->assertCount(1, User::all());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => $request['name'],
                    'email' => $request['email'],
                ]
            ]);
    }

    /** @test */
    public function a_name_is_required()
    {
        $request = array_merge($this->getData(), [
            'name' => '',
        ]);

        $response = $this->postJson('/api/register', $request);

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
        $request = array_merge($this->getData(), [
            'email' => '',
        ]);

        $response = $this->postJson('/api/register', $request);

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
        $request = array_merge($this->getData(), [
            'email' => 'first_user.com',
        ]);
        $response = $this->postJson('/api/register', $request);
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
        $request1 = $this->getData();
        $this->postJson('/api/register', $request1);

        $request2 = array_merge($request1, [
            'name' => 'second_user',
            'password' => 'second-user',
            'password_confirmation' => 'second-user',
        ]);

        $response = $this->postJson('/api/register', $request2);

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
        $request = array_merge($this->getData(), [
            'password' => '',
        ]);
        $response = $this->postJson('/api/register', $request);

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
        $request = array_merge($this->getData(), [
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);
        $response = $this->postJson('/api/register', $request);

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
        $request = array_merge($this->getData(), [
            'password_confirmation' => 'new-password',
        ]);

        $response = $this->postJson('/api/register', $request);

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

        $this->postJson('/api/register', $this->getData());

        $hash = Hash::check('super-secret', User::first()->password);
        $this->assertEquals(true, $hash);
    }

    private function getData()
    {
        return [
            'name' => 'first_user',
            'email' => 'first_user@example.net',
            'password' => 'super-secret',
            'password_confirmation' => 'super-secret',
        ];
    }

}
