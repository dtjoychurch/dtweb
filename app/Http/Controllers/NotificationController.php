<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        AppNotification::where('user_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        return view('front.notifications.index', compact('notifications'));
    }
}
