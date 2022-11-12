<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_request_password_reset_link()
    {
//        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $response = $this->postJson('/api/password-reset-email', [
            'email' => $user->email,
        ]);

        Mail::fake();
//        Mail::assertNothingSent();
        $mail = new PasswordResetMail();
//        Mail::to('new_email@mail.com')->send(new PasswordResetMail());
//        Mail::assertSent(PasswordResetMail::class);
//        , function ($mail) use ($user) {
//            $mail->hasTo($user);
//        });
        $response->assertStatus(200);
    }
}
