<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipCommentRequest;
use App\Http\Resources\DiscipleshipCommentResource;
use App\Models\DiscipleshipComment;
use App\Models\DiscipleshipSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(StoreDiscipleshipCommentRequest $request, DiscipleshipSession $session): JsonResponse
    {
        $this->authorize('create', [DiscipleshipComment::class, $session]);

        $comment = $session->comments()->create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return response()->json(['data' => new DiscipleshipCommentResource($comment->load('user'))], 201);
    }

    public function update(StoreDiscipleshipCommentRequest $request, DiscipleshipComment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return response()->json(['data' => new DiscipleshipCommentResource($comment->load('user'))]);
    }

    public function destroy(DiscipleshipComment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => '已刪除留言。']);
    }
}
