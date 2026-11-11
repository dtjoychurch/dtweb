<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function index()
    {
        $settings = NotificationSetting::orderBy('label')->get();

        return view('admin.notification-settings.index', compact('settings'));
    }

    public function update(Request $request, NotificationSetting $notificationSetting): RedirectResponse
    {
        $data = $request->validate([
            'message_template' => ['required', 'string', 'max:500'],
            'is_enabled' => ['sometimes', 'boolean'],
        ]);

        $notificationSetting->update([
            'message_template' => $data['message_template'],
            'is_enabled' => $request->boolean('is_enabled'),
        ]);

        return redirect()->route('admin.notification-settings.index')->with('status', '已更新通知設定。');
    }
}
