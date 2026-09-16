<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscipleshipNote;
use Illuminate\Http\JsonResponse;

/**
 * Admin moderation only — mirrors {@see \App\Http\Controllers\Admin\NoteController}.
 * Content is never exposed; only metadata is selected, and admins delete via
 * the `moderate` policy ability rather than `delete` (which stays owner-only).
 */
class NoteController extends Controller
{
    public function index(): JsonResponse
    {
        $notes = DiscipleshipNote::query()
            ->select(['id', 'user_id', 'session_id', 'title', 'created_at'])
            ->with(['user:id,name', 'session:id,relationship_id,session_date'])
            ->latest()
            ->paginate(20);

        return response()->json(['data' => $notes]);
    }

    public function destroy(DiscipleshipNote $note): JsonResponse
    {
        $this->authorize('moderate', $note);

        $note->delete();

        return response()->json(['message' => '已刪除筆記（內容未曾被檢視）。']);
    }
}
