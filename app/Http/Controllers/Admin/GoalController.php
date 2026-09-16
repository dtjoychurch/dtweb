<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipGoal::class, 'goal');
    }

    public function index(Request $request)
    {
        $goals = DiscipleshipGoal::with(['relationship.mentor', 'relationship.disciple', 'creator'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('relationship.mentor', fn ($mq) => $mq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('relationship.disciple', fn ($dq) => $dq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('creator', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $relationships = DiscipleshipRelationship::with(['mentor', 'disciple'])->get();

        return view('admin.goals.index', compact('goals', 'relationships'));
    }

    public function edit(DiscipleshipGoal $goal)
    {
        return view('admin.goals.edit', compact('goal'));
    }

    public function update(Request $request, DiscipleshipGoal $goal): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'visibility' => ['required', 'in:shared,private'],
        ]);

        $goal->update($data);

        return redirect()->route('admin.goals.index')->with('status', '已更新門訓目標。');
    }

    public function destroy(DiscipleshipGoal $goal): RedirectResponse
    {
        $goal->delete();

        return redirect()->route('admin.goals.index')->with('status', '已刪除門訓目標。');
    }
}
