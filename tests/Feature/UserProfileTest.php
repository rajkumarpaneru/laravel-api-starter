<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_his_profile()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user-profile');

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
    public function a_user_get_401_unauthenticated_if_not_logged_in()
    {
        $response = $this->getJson('/api/user-profile');

        $response->assertStatus(401)
            ->assertJson([
                'message' => "Unauthenticated."
            ]);
    }

    /** @test */
    public function a_user_can_update_his_profile()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/user-profile', [
            'name' => 'new_user_name',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'new_user_name',
                    'email' => $user->email,
                ]
            ]);
    }

    /** @test */
    public function a_name_field_is_required()
    {
//        $this->withoutExceptionHandling();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->putJson('/api/user-profile', [
            'name' => '',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => 'The name field is required.',
                ]
            ]);
    }
}
