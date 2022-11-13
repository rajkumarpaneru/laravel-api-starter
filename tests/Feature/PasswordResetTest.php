<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Mail\Mailable;
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

//        Mail::assertNothingSent();
//        $mail = new PasswordResetMail();
//        Mail::to('new_email@mail.com')->send(new PasswordResetMail());
//        $response->assertStatus(200);
        Mail::assertSent(PasswordResetMail::class);
//        , function ($mail) use ($user) {
//            $mail->hasTo($user);
//        });
//        $response->assertStatus(200);


        // Arrange
//        Mail::fake();

//        $mailable = new class() extends Mailable {};

        // Act (can be a model action or something)
//        $sent = Mail::to('text@example.com')->send(PasswordResetMail::class);
//        Mail::assertSent(PasswordResetMail::class);
    }
}
