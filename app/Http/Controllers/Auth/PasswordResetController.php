<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Throwable;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('front.auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // 不管信箱存不存在都回一樣的成功訊息，避免被拿來探測哪些信箱有註冊過。
        // 寄信本身（例如 Resend 網域限制、額度用完、網路問題）失敗時也不該讓使用者
        // 看到原始的 500 錯誤頁，一律導回同一個乾淨的訊息並把細節寫進 log。
        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            Log::error('密碼重設信寄送失敗：'.$e->getMessage());
        }

        return back()->with('status', '如果這個信箱有註冊過帳號，重設密碼的連結已經寄出，請去收信。');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('front.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['新密碼不能跟原本的密碼一樣，請換一組。'],
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        return redirect()->route('login')->with('status', '密碼已經重設成功，請用新密碼登入。');
    }
}
