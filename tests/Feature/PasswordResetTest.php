<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        Mail::assertSent(PasswordResetMail::class);
//            , function ($mail) use ($user) {
//                $mail->hasTo($user);
//            });

        // Assert a message was sent to given email addresses
        Mail::assertSent(PasswordResetMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                $mail->hasFrom('jeffrey@example.com');
        });
        $response->assertStatus(200);
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

        $mailable = new PasswordResetMail($password_resets_row->token);
        $mailable->assertFrom('jeffrey@example.com');
        $mailable->assertHasSubject('Password Reset Link');
        $mailable->assertSeeInHtml($password_resets_row->token);
    }
}
