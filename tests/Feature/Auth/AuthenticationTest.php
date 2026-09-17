<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('discipleship.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_records_last_login_time_and_ip(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $this->assertNull($user->last_login_at);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $user->refresh();
        $this->assertNotNull($user->last_login_at);
        $this->assertNotNull($user->last_login_ip);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_guest_cannot_view_discipleship_pages(): void
    {
        $response = $this->get('/discipleship');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_view_notes_or_journey(): void
    {
        $this->get('/notes')->assertRedirect('/login');
        $this->get('/journey')->assertRedirect('/login');
    }
}
