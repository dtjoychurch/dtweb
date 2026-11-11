<?php

namespace App\Http\Controllers;

use App\Models\DiscipleshipRelationship;
use App\Models\RelationshipInvite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelationshipInviteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $myInvites = RelationshipInvite::where('created_by', $user->id)->latest()->get();
        $pendingCount = RelationshipInvite::pendingSubmissionCountFor($user);
        $maxPending = RelationshipInvite::MAX_PENDING_PER_USER;

        return view('front.discipleship.invites.index', compact('myInvites', 'pendingCount', 'maxPending'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'role' => ['required', 'in:mentor,disciple'],
        ]);

        if (RelationshipInvite::pendingSubmissionCountFor($user) >= RelationshipInvite::MAX_PENDING_PER_USER) {
            return back()->withErrors([
                'role' => '你目前已經有 '.RelationshipInvite::MAX_PENDING_PER_USER.' 筆門訓關係申請在等待處理，請等其中幾筆處理完再送出新的。',
            ]);
        }

        RelationshipInvite::create([
            'code' => RelationshipInvite::generateUniqueCode(),
            'created_by' => $user->id,
            'role' => $data['role'],
            'status' => 'pending',
            'expires_at' => RelationshipInvite::defaultExpiry(),
        ]);

        return redirect()->route('discipleship.invites.index')->with('status', '已產生邀請碼，請把這個代碼分享給對方。');
    }

    public function redeem(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $invite = RelationshipInvite::where('code', strtoupper(trim($data['code'])))->first();

        if (! $invite || $invite->status !== 'pending') {
            return back()->withErrors(['code' => '這個邀請碼不存在，或已經被使用過了。']);
        }

        if ($invite->isExpired()) {
            $invite->update(['status' => 'expired']);

            return back()->withErrors(['code' => '這個邀請碼已經過期了，請對方重新產生一個。']);
        }

        if ($invite->created_by === $user->id) {
            return back()->withErrors(['code' => '不能使用自己產生的邀請碼。']);
        }

        if (RelationshipInvite::pendingSubmissionCountFor($user) >= RelationshipInvite::MAX_PENDING_PER_USER) {
            return back()->withErrors([
                'code' => '你目前已經有 '.RelationshipInvite::MAX_PENDING_PER_USER.' 筆門訓關係申請在等待處理，請等其中幾筆處理完再兌換新的。',
            ]);
        }

        $mentorId = $invite->role === 'mentor' ? $invite->created_by : $user->id;
        $discipleId = $invite->role === 'mentor' ? $user->id : $invite->created_by;

        $relationship = DiscipleshipRelationship::create([
            'mentor_id' => $mentorId,
            'disciple_id' => $discipleId,
            'started_at' => now(),
            'status' => 'active',
            'approval_status' => 'pending',
        ]);

        $invite->update([
            'status' => 'redeemed',
            'redeemed_by' => $user->id,
            'redeemed_at' => now(),
            'relationship_id' => $relationship->id,
        ]);

        return redirect()->route('discipleship.invites.index')->with('status', '已送出門訓關係申請，等待管理員審核。');
    }

    public function destroy(RelationshipInvite $invite): RedirectResponse
    {
        abort_unless($invite->created_by === Auth::id(), 403);
        abort_unless($invite->status === 'pending', 404);

        $invite->update(['status' => 'cancelled']);

        return redirect()->route('discipleship.invites.index')->with('status', '已取消這個邀請碼。');
    }
}
