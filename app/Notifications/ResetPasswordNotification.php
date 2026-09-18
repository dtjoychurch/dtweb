<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('重設密碼通知 - '.config('app.name'))
            ->greeting('你好！')
            ->line('我們收到了一個針對你帳號的密碼重設請求。')
            ->action('重設密碼', $url)
            ->line('這個重設連結會在 60 分鐘後失效。')
            ->line('如果你沒有提出這個請求，不用做任何事，你的密碼不會被更動。')
            ->salutation(config('app.name').' 敬上');
    }
}
