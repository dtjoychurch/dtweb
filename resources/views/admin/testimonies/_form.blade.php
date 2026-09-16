@php $testimony = $testimony ?? null; @endphp

@if ($testimony?->image_path)
  <div class="mb-3">
    <img src="{{ asset($testimony->image_path) }}" alt="" style="max-width: 320px; max-height: 180px; object-fit: cover;" class="rounded shadow-sm">
  </div>
@endif

<div class="mb-3">
  <label class="form-label">圖片（選填{{ $testimony ? '，留空表示不更換' : '' }}）</label>
  <input type="file" name="image" class="form-control" accept="image/*">
</div>

<div class="mb-3">
  <label class="form-label">標題</label>
  <input type="text" name="title" class="form-control" value="{{ old('title', $testimony->title ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">見證內容</label>
  <textarea name="content" class="form-control" rows="6" required>{{ old('content', $testimony->content ?? '') }}</textarea>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">排序（數字越小越前面）</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $testimony->sort_order ?? 0) }}" min="0">
  </div>
  <div class="col-md-4 mb-3 d-flex align-items-end">
    <div class="form-check">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" {{ old('is_active', $testimony->is_active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_active">啟用（顯示在首頁）</label>
    </div>
  </div>
</div>

<button type="submit" class="btn btn-dark">儲存</button>
<a href="{{ route('admin.testimonies.index') }}" class="btn btn-link text-muted">取消</a>
