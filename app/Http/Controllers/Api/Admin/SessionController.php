<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipSessionRequest;
use App\Http\Resources\DiscipleshipSessionResource;
use App\Models\DiscipleshipSession;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipSession::class, 'session');
    }

    public function index(): JsonResponse
    {
        $sessions = DiscipleshipSession::with(['relationship.mentor', 'relationship.disciple', 'creator'])
            ->latest('session_date')
            ->paginate(15);

        return response()->json(['data' => DiscipleshipSessionResource::collection($sessions)]);
    }

    public function update(StoreDiscipleshipSessionRequest $request, DiscipleshipSession $session): JsonResponse
    {
        $session->update($request->validated());

        return response()->json(['data' => new DiscipleshipSessionResource($session)]);
    }

    public function destroy(DiscipleshipSession $session): JsonResponse
    {
        $session->delete();

        return response()->json(['message' => '已刪除門訓紀錄。']);
    }
}
