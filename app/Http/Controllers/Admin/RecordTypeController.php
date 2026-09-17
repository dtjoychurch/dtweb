<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipRecordTypeRequest;
use App\Models\DiscipleshipRecordType;
use Illuminate\Http\RedirectResponse;

class RecordTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipRecordType::class, 'record_type');
    }

    public function index()
    {
        $recordTypes = DiscipleshipRecordType::withCount('records')->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.record-types.index', compact('recordTypes'));
    }

    public function create()
    {
        return view('admin.record-types.create');
    }

    public function store(StoreDiscipleshipRecordTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        DiscipleshipRecordType::create($data);

        return redirect()->route('admin.record-types.index')->with('status', '已新增紀錄類型。');
    }

    public function edit(DiscipleshipRecordType $recordType)
    {
        return view('admin.record-types.edit', compact('recordType'));
    }

    public function update(StoreDiscipleshipRecordTypeRequest $request, DiscipleshipRecordType $recordType): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);

        $recordType->update($data);

        return redirect()->route('admin.record-types.index')->with('status', '已更新紀錄類型。');
    }

    public function destroy(DiscipleshipRecordType $recordType): RedirectResponse
    {
        if ($recordType->records()->withTrashed()->exists()) {
            return back()->withErrors(['record_type' => '這個類型還有紀錄在使用中，無法刪除。可以先把它停用。']);
        }

        $recordType->delete();

        return redirect()->route('admin.record-types.index')->with('status', '已刪除紀錄類型。');
    }
}
