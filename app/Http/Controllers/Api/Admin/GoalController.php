<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscipleshipGoalResource;
use App\Models\DiscipleshipGoal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipGoal::class, 'goal');
    }

    public function index(): JsonResponse
    {
        $goals = DiscipleshipGoal::with(['relationship.mentor', 'relationship.disciple', 'creator'])
            ->latest()
            ->paginate(15);

        return response()->json(['data' => DiscipleshipGoalResource::collection($goals)]);
    }

    public function update(Request $request, DiscipleshipGoal $goal): JsonResponse
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

        return response()->json(['data' => new DiscipleshipGoalResource($goal)]);
    }

    public function destroy(DiscipleshipGoal $goal): JsonResponse
    {
        $goal->delete();

        return response()->json(['message' => '已刪除門訓目標。']);
    }
}
