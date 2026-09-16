<?php

namespace App\Http\Controllers;

use App\Models\DiscipleshipRecord;
use Illuminate\Support\Facades\Auth;

class JourneyController extends Controller
{
    /**
     * 生命歷程：這是「我」身為 disciple 的成長紀錄，不包含我作為 mentor
     * 帶別人的那些關係——那些是對方的生命歷程，不是我的。
     */
    public function index()
    {
        $user = Auth::user();
        $relationshipIds = $user->discipleRelationships()->pluck('id');

        $records = DiscipleshipRecord::whereIn('relationship_id', $relationshipIds)
            ->with(['relationship.mentor', 'relationship.disciple', 'creator'])
            ->where(function ($query) use ($user) {
                $query->where('visibility', 'shared')
                    ->orWhere('created_by', $user->id);
            })
            ->orderByDesc('occurred_at')
            ->get();

        return view('front.journey.index', compact('records'));
    }
}
