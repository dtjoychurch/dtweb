<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipRelationshipRequest;
use App\Http\Resources\DiscipleshipRelationshipResource;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\JsonResponse;

class RelationshipController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipRelationship::class, 'relationship');
    }

    public function index(): JsonResponse
    {
        $relationships = DiscipleshipRelationship::with(['mentor', 'disciple'])->withCount('sessions')->paginate(15);

        return response()->json(['data' => DiscipleshipRelationshipResource::collection($relationships)]);
    }

    public function show(DiscipleshipRelationship $relationship): JsonResponse
    {
        return response()->json(['data' => new DiscipleshipRelationshipResource($relationship->load(['mentor', 'disciple']))]);
    }

    public function store(StoreDiscipleshipRelationshipRequest $request): JsonResponse
    {
        $relationship = DiscipleshipRelationship::create($request->validated());

        return response()->json(['data' => new DiscipleshipRelationshipResource($relationship)], 201);
    }

    public function update(StoreDiscipleshipRelationshipRequest $request, DiscipleshipRelationship $relationship): JsonResponse
    {
        $relationship->update($request->validated());

        return response()->json(['data' => new DiscipleshipRelationshipResource($relationship)]);
    }

    public function destroy(DiscipleshipRelationship $relationship): JsonResponse
    {
        $relationship->delete();

        return response()->json(['message' => '已刪除門訓關係。']);
    }
}
