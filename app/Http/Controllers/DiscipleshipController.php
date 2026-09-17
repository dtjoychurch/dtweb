<?php

namespace App\Http\Controllers;

use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipRecordType;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscipleshipController extends Controller
{
    /**
     * 我的門訓：目前使用者身為 mentor 或 disciple 的所有關係。
     */
    public function index()
    {
        $user = Auth::user();

        $relationships = DiscipleshipRelationship::with(['mentor', 'disciple'])
            ->where('mentor_id', $user->id)
            ->orWhere('disciple_id', $user->id)
            ->withCount('sessions')
            ->get()
            ->map(function (DiscipleshipRelationship $relationship) use ($user) {
                $relationship->counterpart = $relationship->counterpartFor($user);
                $relationship->latest_session = $relationship->sessions()->latest('session_date')->first();
                $relationship->role_label = $relationship->mentor_id === $user->id ? 'mentor' : 'disciple';

                return $relationship;
            });

        return view('front.discipleship.index', compact('relationships'));
    }

    /**
     * 門訓關係詳細頁：基本資料 + 混合時間軸（Session / Record）。
     */
    public function show(DiscipleshipRelationship $relationship)
    {
        $this->authorize('view', $relationship);

        $user = Auth::user();
        $relationship->load(['mentor', 'disciple']);

        $sessions = $relationship->sessions()->orderBy('session_date')->get()
            ->values()
            ->map(function ($session, $index) {
                $session->ordinal_number = $index + 1;

                return $session;
            });

        $records = $relationship->records()
            ->with(['creator', 'type'])
            ->get()
            ->filter(fn ($record) => $record->isVisibleTo($user));

        $goals = $relationship->goals()
            ->with('creator')
            ->get()
            ->filter(fn ($goal) => $goal->isVisibleTo($user));

        $timeline = $sessions->map(fn ($session) => [
            'type' => 'session',
            'date' => $session->session_date,
            'model' => $session,
        ])->concat($records->map(fn ($record) => [
            'type' => 'record',
            'date' => $record->occurred_at,
            'model' => $record,
        ]))->sortByDesc(fn ($item) => $item['date'])->values();

        $recordTypes = DiscipleshipRecordType::where('is_active', true)->orderBy('sort_order')->get();

        return view('front.discipleship.show', compact('relationship', 'timeline', 'goals', 'sessions', 'recordTypes'));
    }
}
