<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscipleshipNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Admin moderation only. Deliberately does NOT expose note content anywhere
 * (index selects metadata columns only, no show/edit route exists) — see
 * spec §16: private notes must stay private even from admins. Admins may
 * only see that a note exists and remove it, via {@see DiscipleshipNotePolicy::moderate()}.
 */
class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = DiscipleshipNote::query()
            ->select(['id', 'user_id', 'session_id', 'title', 'created_at'])
            ->with(['user:id,name', 'session:id,relationship_id,session_date'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.notes.index', compact('notes'));
    }

    public function destroy(DiscipleshipNote $note): RedirectResponse
    {
        $this->authorize('moderate', $note);

        $note->delete();

        return redirect()->route('admin.notes.index')->with('status', '已刪除筆記（內容未曾被檢視）。');
    }
}
