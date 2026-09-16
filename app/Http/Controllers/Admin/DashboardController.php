<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'relationships' => DiscipleshipRelationship::count(),
            'active_relationships' => DiscipleshipRelationship::where('status', 'active')->count(),
            'sessions_this_month' => DiscipleshipSession::whereBetween('session_date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])->count(),
            'records' => DiscipleshipRecord::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
