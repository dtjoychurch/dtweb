<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_form_can_be_rendered(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_link_is_sent_to_an_existing_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_requesting_a_reset_link_for_an_unknown_email_does_not_error(): void
    {
        Notification::fake();

        $response = $this->post(route('password.email'), ['email' => 'nobody@example.com']);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        Notification::assertNothingSent();
    }

    public function test_mail_delivery_failure_does_not_crash_the_page(): void
    {
        $user = User::factory()->create();

        Password::shouldReceive('sendResetLink')
            ->once()
            ->andThrow(new \Exception('Testing domain restriction'));

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_user_can_reset_password_with_a_valid_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);
        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_user_cannot_reset_password_to_the_same_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('same-password')]);
        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'same-password',
            'password_confirmation' => 'same-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('same-password', $user->fresh()->password));
    }

    public function test_user_cannot_reset_password_with_an_invalid_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
