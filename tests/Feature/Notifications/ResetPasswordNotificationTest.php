<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetPasswordNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_mail_content_is_in_traditional_chinese(): void
    {
        $user = User::factory()->create();
        $notification = new ResetPasswordNotification('a-fake-token');

        $mail = $notification->toMail($user);

        $this->assertStringContainsString('重設密碼通知', $mail->subject);
        $this->assertStringContainsString('你好', $mail->greeting);
        $this->assertStringContainsString('重設密碼', $mail->actionText);
        $this->assertStringContainsString('敬上', $mail->salutation);
    }
}
