<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    /** @test */
    public function a_user_can_change_its_password()
    {
//        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $user->password = Hash::make('old-password');
        $user->save();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/password-change', [
            'old_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password'
        ]);

        //assert that new password is new-password

        $hash = Hash::check('new-password', $user->password);
        $this->assertEquals(true, $hash);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
    }
}
