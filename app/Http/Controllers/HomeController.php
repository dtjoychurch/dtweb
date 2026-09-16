<?php

namespace App\Http\Controllers;

use App\Models\DiscipleshipRelationship;
use App\Models\HeroSlide;
use App\Models\Testimony;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides = HeroSlide::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
        $testimonies = Testimony::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        $recentSessions = collect();

        if (Auth::check()) {
            $user = Auth::user();
            $relationshipIds = $user->discipleshipRelationships()->pluck('id');

            $recentSessions = \App\Models\DiscipleshipSession::whereIn('relationship_id', $relationshipIds)
                ->with(['relationship.mentor', 'relationship.disciple'])
                ->latest('session_date')
                ->limit(3)
                ->get();
        }

        return view('front.home.index', compact('heroSlides', 'testimonies', 'recentSessions'));
    }
}
