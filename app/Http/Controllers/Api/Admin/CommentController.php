<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscipleshipCommentResource;
use App\Models\DiscipleshipComment;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipComment::class, 'comment', ['except' => ['create', 'store', 'edit', 'update', 'show']]);
    }

    public function index(): JsonResponse
    {
        $comments = DiscipleshipComment::with(['user', 'session.relationship'])->latest()->paginate(20);

        return response()->json(['data' => DiscipleshipCommentResource::collection($comments)]);
    }

    public function destroy(DiscipleshipComment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(['message' => '已刪除留言。']);
    }
}
