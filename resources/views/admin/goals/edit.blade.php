
@extends('admin.layouts.app')

@section('title', '編輯門訓目標')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯門訓目標</h2>
<p class="text-muted">{{ $goal->relationship->mentor->name }} × {{ $goal->relationship->disciple->name }}</p>

<form method="POST" action="{{ route('admin.goals.update', $goal) }}" style="max-width: 600px;">
  @csrf
  @method('PUT')

  <div class="mb-3">
    <label class="form-label">標題</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $goal->title) }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">描述</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $goal->description) }}</textarea>
  </div>

  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">狀態</label>
      <select name="status" class="form-select">
        <option value="pending" {{ old('status', $goal->status) === 'pending' ? 'selected' : '' }}>尚未開始</option>
        <option value="in_progress" {{ old('status', $goal->status) === 'in_progress' ? 'selected' : '' }}>進行中</option>
        <option value="completed" {{ old('status', $goal->status) === 'completed' ? 'selected' : '' }}>已完成</option>
        <option value="cancelled" {{ old('status', $goal->status) === 'cancelled' ? 'selected' : '' }}>已取消</option>
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">開始日期</label>
      <input type="date" name="started_at" class="form-control" value="{{ old('started_at', $goal->started_at?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">完成日期</label>
      <input type="date" name="completed_at" class="form-control" value="{{ old('completed_at', $goal->completed_at?->format('Y-m-d')) }}">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">可見範圍</label>
    <select name="visibility" class="form-select">
      <option value="shared" {{ old('visibility', $goal->visibility) === 'shared' ? 'selected' : '' }}>雙方可見</option>
      <option value="private" {{ old('visibility', $goal->visibility) === 'private' ? 'selected' : '' }}>私人</option>
    </select>
  </div>

  <button type="submit" class="btn btn-dark">儲存</button>
  <a href="{{ route('admin.goals.index') }}" class="btn btn-link text-muted">取消</a>
</form>
@endsection
