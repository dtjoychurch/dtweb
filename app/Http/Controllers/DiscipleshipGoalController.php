<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiscipleshipGoalRequest;
use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DiscipleshipGoalController extends Controller
{
    public function store(StoreDiscipleshipGoalRequest $request, DiscipleshipRelationship $relationship): RedirectResponse
    {
        $this->authorize('create', [DiscipleshipGoal::class, $relationship]);

        $relationship->goals()->create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('discipleship.show', $relationship)->with('status', '已新增門訓目標。');
    }

    public function update(StoreDiscipleshipGoalRequest $request, DiscipleshipRelationship $relationship, DiscipleshipGoal $goal): RedirectResponse
    {
        abort_unless($goal->relationship_id === $relationship->id, 404);

        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return redirect()->route('discipleship.show', $relationship)->with('status', '已更新門訓目標。');
    }

    public function destroy(DiscipleshipRelationship $relationship, DiscipleshipGoal $goal): RedirectResponse
    {
        abort_unless($goal->relationship_id === $relationship->id, 404);

        $this->authorize('delete', $goal);

        $goal->delete();

        return redirect()->route('discipleship.show', $relationship)->with('status', '已刪除門訓目標。');
    }
}
