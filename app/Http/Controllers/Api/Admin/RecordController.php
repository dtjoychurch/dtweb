<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiscipleshipRecordResource;
use App\Models\DiscipleshipRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipRecord::class, 'record');
    }

    public function index(): JsonResponse
    {
        $records = DiscipleshipRecord::with(['relationship.mentor', 'relationship.disciple', 'creator'])
            ->latest('occurred_at')
            ->paginate(15);

        return response()->json(['data' => DiscipleshipRecordResource::collection($records)]);
    }

    public function update(Request $request, DiscipleshipRecord $record): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', DiscipleshipRecord::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'visibility' => ['required', 'in:shared,private'],
            'occurred_at' => ['required', 'date'],
        ]);

        $record->update($data);

        return response()->json(['data' => new DiscipleshipRecordResource($record)]);
    }

    public function destroy(DiscipleshipRecord $record): JsonResponse
    {
        $record->delete();

        return response()->json(['message' => '已刪除紀錄。']);
    }
}
