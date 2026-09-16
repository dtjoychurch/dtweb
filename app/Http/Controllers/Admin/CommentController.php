<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscipleshipComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipComment::class, 'comment', ['except' => ['create', 'store', 'edit', 'update', 'show']]);
    }

    public function index(Request $request)
    {
        $comments = DiscipleshipComment::with(['user', 'session.relationship'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function destroy(DiscipleshipComment $comment): RedirectResponse
    {
        $comment->delete();

        return redirect()->route('admin.comments.index')->with('status', '已刪除留言。');
    }
}
