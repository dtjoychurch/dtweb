<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRecordType;
use App\Models\DiscipleshipRelationship;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecordController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipRecord::class, 'record');
    }

    public function index(Request $request)
    {
        $records = DiscipleshipRecord::with(['relationship.mentor', 'relationship.disciple', 'creator', 'type'])
            ->when($request->filled('type_id'), fn ($q) => $q->where('type_id', $request->integer('type_id')))
            ->when($request->filled('relationship_id'), fn ($q) => $q->where('relationship_id', $request->integer('relationship_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('relationship.mentor', fn ($mq) => $mq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('relationship.disciple', fn ($dq) => $dq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('creator', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('occurred_at')
            ->paginate(15)
            ->withQueryString();

        $relationships = DiscipleshipRelationship::with(['mentor', 'disciple'])->get();
        $types = DiscipleshipRecordType::orderBy('sort_order')->get();

        return view('admin.records.index', compact('records', 'relationships', 'types'));
    }

    public function edit(DiscipleshipRecord $record)
    {
        $types = DiscipleshipRecordType::orderBy('sort_order')->get();

        return view('admin.records.edit', compact('record', 'types'));
    }

    public function update(Request $request, DiscipleshipRecord $record): RedirectResponse
    {
        $data = $request->validate([
            'type_id' => ['required', Rule::exists('discipleship_record_types', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'visibility' => ['required', 'in:shared,private'],
            'occurred_at' => ['required', 'date'],
        ]);

        $record->update($data);

        return redirect()->route('admin.records.index')->with('status', '已更新生命歷程紀錄。');
    }

    public function destroy(DiscipleshipRecord $record): RedirectResponse
    {
        $record->delete();

        return redirect()->route('admin.records.index')->with('status', '已刪除紀錄（可還原）。');
    }
}
