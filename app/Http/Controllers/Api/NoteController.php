<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipNoteRequest;
use App\Http\Resources\DiscipleshipNoteResource;
use App\Models\DiscipleshipNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notes = $request->user()->notes()->latest()->paginate(15);

        return response()->json([
            'data' => DiscipleshipNoteResource::collection($notes),
            'meta' => ['current_page' => $notes->currentPage(), 'last_page' => $notes->lastPage(), 'total' => $notes->total()],
        ]);
    }

    public function store(StoreDiscipleshipNoteRequest $request): JsonResponse
    {
        $note = $request->user()->notes()->create($request->validated());

        return response()->json(['data' => new DiscipleshipNoteResource($note)], 201);
    }

    public function update(StoreDiscipleshipNoteRequest $request, DiscipleshipNote $note): JsonResponse
    {
        $this->authorize('update', $note);

        $note->update($request->validated());

        return response()->json(['data' => new DiscipleshipNoteResource($note)]);
    }

    public function destroy(DiscipleshipNote $note): JsonResponse
    {
        $this->authorize('delete', $note);

        $note->delete();

        return response()->json(['message' => '已刪除筆記。']);
    }
}
