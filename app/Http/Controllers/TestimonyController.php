<?php

namespace App\Http\Controllers;

use App\Models\Testimony;
use Illuminate\Support\Facades\Auth;

class TestimonyController extends Controller
{
    public function show(Testimony $testimony)
    {
        // 未啟用的見證只有 admin 能預覽，一般訪客/使用者看不到。
        abort_unless($testimony->is_active || Auth::user()?->isAdmin(), 404);

        return view('front.testimonies.show', compact('testimony'));
    }
}
