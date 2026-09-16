<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestimonyRequest;
use App\Models\Testimony;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class TestimonyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Testimony::class, 'testimony');
    }

    public function index()
    {
        $testimonies = Testimony::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.testimonies.index', compact('testimonies'));
    }

    public function create()
    {
        return view('admin.testimonies.create');
    }

    public function store(StoreTestimonyRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except('image')->all();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = 'uploads/'.$request->file('image')->store('testimonies', 'uploads');
        }

        Testimony::create($data);

        return redirect()->route('admin.testimonies.index')->with('status', '已新增見證分享。');
    }

    public function edit(Testimony $testimony)
    {
        return view('admin.testimonies.edit', compact('testimony'));
    }

    public function update(StoreTestimonyRequest $request, Testimony $testimony): RedirectResponse
    {
        $data = collect($request->validated())->except('image')->all();
        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('image')) {
            $this->deleteImage($testimony->image_path);
            $data['image_path'] = 'uploads/'.$request->file('image')->store('testimonies', 'uploads');
        }

        $testimony->update($data);

        return redirect()->route('admin.testimonies.index')->with('status', '已更新見證分享。');
    }

    public function destroy(Testimony $testimony): RedirectResponse
    {
        $this->deleteImage($testimony->image_path);
        $testimony->delete();

        return redirect()->route('admin.testimonies.index')->with('status', '已刪除見證分享。');
    }

    private function deleteImage(?string $publicPath): void
    {
        if ($publicPath && str_starts_with($publicPath, 'uploads/')) {
            Storage::disk('uploads')->delete(str_replace('uploads/', '', $publicPath));
        }
    }
}
