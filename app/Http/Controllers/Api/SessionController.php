<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipSessionRequest;
use App\Http\Resources\DiscipleshipSessionResource;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index(DiscipleshipRelationship $relationship): JsonResponse
    {
        $this->authorize('view', $relationship);

        $sessions = $relationship->sessions()->orderBy('session_date')->get()->values()
            ->map(function ($session, $index) {
                $session->ordinal_number = $index + 1;

                return $session;
            });

        return response()->json(['data' => DiscipleshipSessionResource::collection($sessions)]);
    }

    public function store(StoreDiscipleshipSessionRequest $request, DiscipleshipRelationship $relationship): JsonResponse
    {
        $this->authorize('create', [DiscipleshipSession::class, $relationship]);

        $session = $relationship->sessions()->create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return response()->json(['data' => new DiscipleshipSessionResource($session)], 201);
    }

    public function show(DiscipleshipSession $session): JsonResponse
    {
        $this->authorize('view', $session);

        $session->load(['creator', 'comments.user']);
        $session->ordinal_number = $session->ordinal();

        return response()->json(['data' => new DiscipleshipSessionResource($session)]);
    }

    public function destroy(DiscipleshipSession $session): JsonResponse
    {
        $this->authorize('delete', $session);

        $session->delete();

        return response()->json(['message' => '已刪除這次門訓紀錄。']);
    }
}
