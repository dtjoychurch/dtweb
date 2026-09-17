@php $recordType = $recordType ?? null; @endphp

<div class="mb-3">
  <label class="form-label">名稱</label>
  <input type="text" name="name" class="form-control" value="{{ old('name', $recordType->name ?? '') }}" required>
</div>

<div class="mb-3">
  <label class="form-label">代碼（英文，僅供內部識別，例如 growth）</label>
  <input type="text" name="slug" class="form-control" value="{{ old('slug', $recordType->slug ?? '') }}" required>
</div>

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">排序（數字越小越前面）</label>
    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $recordType->sort_order ?? 0) }}" min="0">
  </div>
  <div class="col-md-6 mb-3 d-flex align-items-end">
    <div class="form-check">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" {{ old('is_active', $recordType->is_active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_active">啟用</label>
    </div>
  </div>
</div>

<button type="submit" class="btn btn-dark">儲存</button>
<a href="{{ route('admin.record-types.index') }}" class="btn btn-link text-muted">取消</a>
