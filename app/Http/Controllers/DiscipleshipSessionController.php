<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\UploadsSessionPhotos;
use App\Http\Requests\StoreDiscipleshipSessionRequest;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DiscipleshipSessionController extends Controller
{
    use UploadsSessionPhotos;

    public function store(StoreDiscipleshipSessionRequest $request, DiscipleshipRelationship $relationship): RedirectResponse
    {
        $this->authorize('create', [DiscipleshipSession::class, $relationship]);

        $session = $relationship->sessions()->create([
            ...collect($request->validated())->except('photos')->all(),
            'created_by' => Auth::id(),
        ]);

        $this->storeSessionPhotos($request->file('photos', []), $session);

        return redirect()
            ->route('discipleship.sessions.show', [$relationship, $session])
            ->with('status', '已新增這次門訓紀錄。');
    }

    public function show(DiscipleshipRelationship $relationship, DiscipleshipSession $session)
    {
        abort_unless($session->relationship_id === $relationship->id, 404);

        $this->authorize('view', $session);

        $user = Auth::user();

        $session->load(['creator', 'comments.user', 'photos.uploader']);

        $note = $session->notes()->where('user_id', $user->id)->first();
        $ordinal = $session->ordinal();

        return view('front.discipleship.sessions.show', compact('relationship', 'session', 'note', 'ordinal'));
    }

    public function destroy(DiscipleshipRelationship $relationship, DiscipleshipSession $session): RedirectResponse
    {
        abort_unless($session->relationship_id === $relationship->id, 404);

        $this->authorize('delete', $session);

        $session->delete();

        return redirect()
            ->route('discipleship.show', $relationship)
            ->with('status', '已刪除這次門訓紀錄（歷史紀錄仍保留，如需還原請聯絡管理員）。');
    }
}
