<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_hashed_not_stored_in_plain_text(): void
    {
        $this->post('/register', [
            'name' => '測試使用者',
            'email' => 'plain-check@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'plain-check@example.com')->firstOrFail();

        $this->assertNotEquals('password123', $user->getRawOriginal('password'));
        $this->assertTrue(Hash::isHashed($user->getRawOriginal('password')));
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_first_ever_user_becomes_admin_automatically(): void
    {
        $this->assertSame(0, User::count());

        $this->post('/register', [
            'name' => '第一個帳號',
            'email' => 'first@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $first = User::where('email', 'first@example.com')->firstOrFail();
        $this->assertSame('admin', $first->role);
    }

    public function test_subsequent_users_are_normal_members(): void
    {
        User::factory()->create();

        $this->post('/register', [
            'name' => '第二個帳號',
            'email' => 'second@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $second = User::where('email', 'second@example.com')->firstOrFail();
        $this->assertSame('member', $second->role);
    }

    public function test_cannot_register_two_accounts_with_the_same_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post('/register', [
            'name' => '第二個帳號',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame(1, User::where('email', 'taken@example.com')->count());
    }

    public function test_registration_is_rate_limited_against_mass_signups(): void
    {
        // Reuse an already-taken email so every attempt fails validation (302
        // back with errors) rather than succeeding and logging the user in —
        // a successful registration would trip the `guest` middleware on the
        // next call and redirect before ever reaching the throttle check,
        // masking whether throttling itself works.
        User::factory()->create(['email' => 'flood@example.com']);

        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/register', [
                'name' => "使用者{$i}",
                'email' => 'flood@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);
            $response->assertStatus(302)->assertSessionHasErrors('email');
        }

        // The 7th attempt within the same minute should be throttled.
        $response = $this->post('/register', [
            'name' => '第七次',
            'email' => 'flood@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(429);
    }
}
