<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscipleshipRecordRequest;
use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DiscipleshipRecordController extends Controller
{
    public function store(StoreDiscipleshipRecordRequest $request, DiscipleshipRelationship $relationship): RedirectResponse
    {
        $this->authorize('create', [DiscipleshipRecord::class, $relationship]);

        $relationship->records()->create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('discipleship.show', $relationship)
            ->with('status', '已新增生命歷程紀錄。');
    }

    public function destroy(DiscipleshipRelationship $relationship, DiscipleshipRecord $record): RedirectResponse
    {
        abort_unless($record->relationship_id === $relationship->id, 404);

        $this->authorize('delete', $record);

        $record->delete();

        return back()->with('status', '已刪除這筆紀錄。');
    }
}
