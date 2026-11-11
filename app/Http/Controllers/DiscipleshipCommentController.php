<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscipleshipCommentRequest;
use App\Models\AppNotification;
use App\Models\DiscipleshipComment;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\NotificationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DiscipleshipCommentController extends Controller
{
    public function store(StoreDiscipleshipCommentRequest $request, DiscipleshipRelationship $relationship, DiscipleshipSession $session): RedirectResponse
    {
        abort_unless($session->relationship_id === $relationship->id, 404);

        $this->authorize('create', [DiscipleshipComment::class, $session]);

        $session->comments()->create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        $this->notifyCounterpartOfNewComment($relationship, $session);

        return back()->with('status', '已新增留言。');
    }

    /**
     * 通知對方「這則門訓紀錄有新留言」——如果這筆門訓本來就對對方隱藏，
     * 就不通知了，不然對方點進去只會看到 403。
     */
    private function notifyCounterpartOfNewComment(DiscipleshipRelationship $relationship, DiscipleshipSession $session): void
    {
        if (! NotificationSetting::isEnabled('session_comment')) {
            return;
        }

        $counterpart = $relationship->counterpartFor(Auth::user());

        if (! $counterpart || ! $session->isVisibleTo($counterpart)) {
            return;
        }

        $message = NotificationSetting::render('session_comment', [
            ':user' => Auth::user()->name,
            ':ordinal' => (string) $session->ordinal(),
        ]);

        if (! $message) {
            return;
        }

        AppNotification::create([
            'user_id' => $counterpart->id,
            'type' => 'session_comment',
            'message' => $message,
            'url' => route('discipleship.sessions.show', [$relationship, $session]),
        ]);
    }

    public function update(StoreDiscipleshipCommentRequest $request, DiscipleshipRelationship $relationship, DiscipleshipSession $session, DiscipleshipComment $comment): RedirectResponse
    {
        abort_unless($comment->session_id === $session->id && $session->relationship_id === $relationship->id, 404);

        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return back()->with('status', '已更新留言。');
    }

    public function destroy(DiscipleshipRelationship $relationship, DiscipleshipSession $session, DiscipleshipComment $comment): RedirectResponse
    {
        abort_unless($comment->session_id === $session->id && $session->relationship_id === $relationship->id, 404);

        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', '已刪除留言。');
    }
}
