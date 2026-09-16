<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipGoalRequest;
use App\Http\Resources\DiscipleshipGoalResource;
use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    /**
     * 目前使用者可見的所有門訓目標（跨越所有關係）。
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $relationshipIds = $user->discipleshipRelationships()->pluck('id');

        $goals = DiscipleshipGoal::whereIn('relationship_id', $relationshipIds)
            ->with('creator')
            ->get()
            ->filter(fn (DiscipleshipGoal $goal) => $goal->isVisibleTo($user))
            ->values();

        return response()->json(['data' => DiscipleshipGoalResource::collection($goals)]);
    }

    public function store(StoreDiscipleshipGoalRequest $request, DiscipleshipRelationship $relationship): JsonResponse
    {
        $this->authorize('create', [DiscipleshipGoal::class, $relationship]);

        $goal = $relationship->goals()->create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return response()->json(['data' => new DiscipleshipGoalResource($goal)], 201);
    }

    public function update(StoreDiscipleshipGoalRequest $request, DiscipleshipGoal $goal): JsonResponse
    {
        $this->authorize('update', $goal);

        $goal->update($request->validated());

        return response()->json(['data' => new DiscipleshipGoalResource($goal)]);
    }

    public function destroy(DiscipleshipGoal $goal): JsonResponse
    {
        $this->authorize('delete', $goal);

        $goal->delete();

        return response()->json(['message' => '已刪除門訓目標。']);
    }
}
