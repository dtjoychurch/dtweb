<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipRecordRequest;
use App\Http\Resources\DiscipleshipRecordResource;
use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    public function store(StoreDiscipleshipRecordRequest $request, DiscipleshipRelationship $relationship): JsonResponse
    {
        $this->authorize('create', [DiscipleshipRecord::class, $relationship]);

        $record = $relationship->records()->create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return response()->json(['data' => new DiscipleshipRecordResource($record)], 201);
    }

    public function destroy(DiscipleshipRecord $record): JsonResponse
    {
        $this->authorize('delete', $record);

        $record->delete();

        return response()->json(['message' => '已刪除生命歷程紀錄。']);
    }
}
