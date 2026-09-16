@php $heroSlide = $heroSlide ?? null; @endphp

@if ($heroSlide?->image_path)
  <div class="mb-3">
    <img src="{{ asset($heroSlide->image_path) }}" alt="" style="max-width: 320px; max-height: 180px; object-fit: cover;" class="rounded shadow-sm">
  </div>
@endif

<div class="mb-3">
  <label class="form-label">圖片{{ $heroSlide ? '（留空表示不更換）' : '' }}</label>
  <input type="file" name="image" class="form-control" accept="image/*" {{ $heroSlide ? '' : 'required' }}>
</div>

<div class="mb-3">
  <label class="form-label">主標題（選填）</label>
  <input type="text" name="title" class="form-control" value="{{ old('title', $heroSlide->title ?? '') }}">
</div>

<div class="mb-3">
  <label class="form-label">副標題（選填）</label>
  <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $heroSlide->subtitle ?? '') }}">
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">排序（數字越小越前面）</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $heroSlide->sort_order ?? 0) }}" min="0">
  </div>
  <div class="col-md-4 mb-3 d-flex align-items-end">
    <div class="form-check">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" {{ old('is_active', $heroSlide->is_active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_active">啟用（顯示在首頁）</label>
    </div>
  </div>
</div>

<button type="submit" class="btn btn-dark">儲存</button>
<a href="{{ route('admin.hero-slides.index') }}" class="btn btn-link text-muted">取消</a>
