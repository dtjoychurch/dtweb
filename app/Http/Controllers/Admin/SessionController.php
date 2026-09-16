<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipSessionRequest;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipSession::class, 'session');
    }

    public function index(Request $request)
    {
        $sessions = DiscipleshipSession::with(['relationship.mentor', 'relationship.disciple', 'creator'])
            ->when($request->filled('relationship_id'), fn ($q) => $q->where('relationship_id', $request->integer('relationship_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('relationship.mentor', fn ($mq) => $mq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('relationship.disciple', fn ($dq) => $dq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('creator', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('session_date')
            ->paginate(15)
            ->withQueryString();

        $relationships = DiscipleshipRelationship::with(['mentor', 'disciple'])->get();

        return view('admin.sessions.index', compact('sessions', 'relationships'));
    }

    public function edit(DiscipleshipSession $session)
    {
        return view('admin.sessions.edit', compact('session'));
    }

    public function update(StoreDiscipleshipSessionRequest $request, DiscipleshipSession $session): RedirectResponse
    {
        $session->update($request->validated());

        return redirect()->route('admin.sessions.index')->with('status', '已更新門訓紀錄。');
    }

    public function destroy(DiscipleshipSession $session): RedirectResponse
    {
        $session->delete();

        return redirect()->route('admin.sessions.index')->with('status', '已刪除門訓紀錄（歷史紀錄仍保留，可還原）。');
    }
}
