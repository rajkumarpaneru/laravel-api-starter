<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_request_password_reset_link()
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/password-reset-email', [
            'email' => $user->email,
        ]);

        //password-reset token is deleted from db
        $this->assertCount(1, DB::table('password_resets')->get());

        // Assert a message was sent to given email addresses
        Mail::assertSent(PasswordResetMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                $mail->hasFrom(config('mail.from.address'));
        });

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'message' => 'Password reset link successfully sent.',
                ]
            ]);
    }

    /** @test */
    public function password_reset_mail_have_defined_contents()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $response = $this->postJson('/api/password-reset-email', [
            'email' => $user->email,
        ]);

        //create a password-reset entry in database
        $this->assertDatabaseCount('password_resets', 1);

        $this->assertDatabaseHas('password_resets', ['email' => $user->email]);

        $password_resets_row = DB::table('password_resets')->first();

        $mailable = new PasswordResetMail($password_resets_row->token, $user->email);
        $mailable->assertFrom(config('mail.from.address'));
        $mailable->assertHasSubject('Password Reset Link');
        $mailable->assertSeeInHtml($password_resets_row->token);
    }

    /** @test */
    public function user_can_reset_password_with_password_reset_token()
    {
//        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        //add password-reset token to db
        $token = Str::random();
        DB::table('password_resets')
            ->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

        $response = $this->postJson('/api/password-reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $hash = Hash::check('new-password', $user->fresh()->password);
        $this->assertEquals(true, $hash);

        //password-reset token is deleted from db
        $this->assertCount(0, DB::table('password_resets')->get());

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
    }

    /** @test */
    public function a_password_field_is_required()
    {
        //        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        $response = $this->postJson('/api/password-reset', [
            'token' => '123456',
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => 'The password field is required.',
                ]
            ]);
    }

    /** @test */
    public function a_password_must_be_confirmed()
    {
        //        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        $response = $this->postJson('/api/password-reset', [
            'token' => '123456',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password1',
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => 'The password confirmation does not match.',
                ]
            ]);
    }

    /** @test */
    public function a_password_must_be_at_least_eight_character_long()
    {
        //        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        $response = $this->postJson('/api/password-reset', [
            'token' => '123456',
            'email' => $user->email,
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => 'The password must be at least 8 characters.',
                ]
            ]);
    }

    /** @test */
    public function an_email_is_required()
    {
        //        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        $response = $this->postJson('/api/password-reset', [
            'token' => '123456',
            'email' => '',
            'password' => '1234567',
            'password_confirmation' => '1234567',
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
        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        $response = $this->postJson('/api/password-reset', [
            'token' => '123456',
            'email' => 'invalid_email',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => 'The email must be a valid email address.',
                ]
            ]);
    }

    /** @test */
    public function a_token_is_required()
    {
        //        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        $response = $this->postJson('/api/password-reset', [
            'token' => '',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'token' => 'The token field is required.',
                ]
            ]);
    }

    /** @test */
    public function a_token_must_be_a_valid_token()
    {
//        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();

        $response = $this->postJson('/api/password-reset', [
            'token' => '123456', //invalid_token
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        //password is not updated with invalid token
        $hash = Hash::check('old-password', $user->fresh()->password);
        $this->assertEquals(true, $hash);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'token' => 'The token is invalid.',
                ]
            ]);
    }

}
