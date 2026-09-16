<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHeroSlideRequest;
use App\Models\HeroSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(HeroSlide::class, 'hero_slide');
    }

    public function index()
    {
        $heroSlides = HeroSlide::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.hero-slides.index', compact('heroSlides'));
    }

    public function create()
    {
        return view('admin.hero-slides.create');
    }

    public function store(StoreHeroSlideRequest $request): RedirectResponse
    {
        $data = collect($request->validated())->except('image')->all();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = 'uploads/'.$request->file('image')->store('hero-slides', 'uploads');
        }

        HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.index')->with('status', '已新增首頁 Hero 圖片。');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.edit', compact('heroSlide'));
    }

    public function update(StoreHeroSlideRequest $request, HeroSlide $heroSlide): RedirectResponse
    {
        $data = collect($request->validated())->except('image')->all();
        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('image')) {
            $this->deleteImage($heroSlide->image_path);
            $data['image_path'] = 'uploads/'.$request->file('image')->store('hero-slides', 'uploads');
        }

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')->with('status', '已更新首頁 Hero 圖片。');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $this->deleteImage($heroSlide->image_path);
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('status', '已刪除首頁 Hero 圖片。');
    }

    private function deleteImage(?string $publicPath): void
    {
        if ($publicPath && str_starts_with($publicPath, 'uploads/')) {
            Storage::disk('uploads')->delete(str_replace('uploads/', '', $publicPath));
        }
    }
}
