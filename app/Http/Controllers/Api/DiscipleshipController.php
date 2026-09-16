<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscipleshipRelationshipResource;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscipleshipController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $relationships = DiscipleshipRelationship::with(['mentor', 'disciple'])
            ->withCount('sessions')
            ->where('mentor_id', $user->id)
            ->orWhere('disciple_id', $user->id)
            ->get();

        return response()->json(['data' => DiscipleshipRelationshipResource::collection($relationships)]);
    }

    public function show(Request $request, DiscipleshipRelationship $relationship): JsonResponse
    {
        $this->authorize('view', $relationship);

        $relationship->load(['mentor', 'disciple'])->loadCount('sessions');

        return response()->json(['data' => new DiscipleshipRelationshipResource($relationship)]);
    }
}
