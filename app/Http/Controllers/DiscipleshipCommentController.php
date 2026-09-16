<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscipleshipCommentRequest;
use App\Models\DiscipleshipComment;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
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

        return back()->with('status', '已新增留言。');
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
