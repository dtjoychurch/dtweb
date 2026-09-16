<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscipleshipRelationshipRequest;
use App\Models\DiscipleshipRelationship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiscipleshipRelationship::class, 'relationship');
    }

    public function index(Request $request)
    {
        $relationships = DiscipleshipRelationship::with(['mentor', 'disciple'])
            ->withCount('sessions')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('mentor', fn ($mq) => $mq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('disciple', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('started_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.relationships.index', compact('relationships'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('admin.relationships.create', compact('users'));
    }

    public function store(StoreDiscipleshipRelationshipRequest $request): RedirectResponse
    {
        DiscipleshipRelationship::create($request->validated());

        return redirect()->route('admin.relationships.index')->with('status', '已新增門訓關係。');
    }

    public function edit(DiscipleshipRelationship $relationship)
    {
        $users = User::orderBy('name')->get();

        return view('admin.relationships.edit', compact('relationship', 'users'));
    }

    public function update(StoreDiscipleshipRelationshipRequest $request, DiscipleshipRelationship $relationship): RedirectResponse
    {
        $relationship->update($request->validated());

        return redirect()->route('admin.relationships.index')->with('status', '已更新門訓關係。');
    }

    public function destroy(DiscipleshipRelationship $relationship): RedirectResponse
    {
        $relationship->delete();

        return redirect()->route('admin.relationships.index')->with('status', '已刪除門訓關係（歷史紀錄仍保留）。');
    }
}
