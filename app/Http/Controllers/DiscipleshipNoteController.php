<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscipleshipNoteRequest;
use App\Models\DiscipleshipNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DiscipleshipNoteController extends Controller
{
    public function index()
    {
        $notes = Auth::user()->notes()
            ->with('session.relationship')
            ->latest()
            ->paginate(10);

        return view('front.notes.index', compact('notes'));
    }

    public function create()
    {
        // Sessions across every relationship the user participates in, for attaching a note to.
        $relationshipIds = Auth::user()->discipleshipRelationships()->pluck('id');
        $sessions = \App\Models\DiscipleshipSession::whereIn('relationship_id', $relationshipIds)
            ->with('relationship')
            ->latest('session_date')
            ->get();

        return view('front.notes.create', compact('sessions'));
    }

    public function store(StoreDiscipleshipNoteRequest $request): RedirectResponse
    {
        Auth::user()->notes()->create($request->validated());

        return redirect()->route('notes.index')->with('status', '已新增筆記。');
    }

    public function edit(DiscipleshipNote $note)
    {
        $this->authorize('update', $note);

        return view('front.notes.edit', compact('note'));
    }

    public function update(StoreDiscipleshipNoteRequest $request, DiscipleshipNote $note): RedirectResponse
    {
        $this->authorize('update', $note);

        $note->update($request->validated());

        return redirect()->route('notes.index')->with('status', '已更新筆記。');
    }

    public function destroy(DiscipleshipNote $note): RedirectResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        return redirect()->route('notes.index')->with('status', '已刪除筆記。');
    }
}
