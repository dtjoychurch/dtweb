
@extends('admin.layouts.app')

@section('title', '編輯生命歷程紀錄')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯生命歷程紀錄</h2>
<p class="text-muted">{{ $record->relationship->mentor->name }} × {{ $record->relationship->disciple->name }}</p>

@php
  $typeLabels = ['growth'=>'成長','struggle'=>'掙扎','reflection'=>'反思','prayer'=>'禱告','milestone'=>'里程碑','observation'=>'觀察','decision'=>'決定','testimony'=>'見證'];
@endphp

<form method="POST" action="{{ route('admin.records.update', $record) }}" style="max-width: 600px;">
  @csrf
  @method('PUT')

  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">類型</label>
      <select name="type" class="form-select">
        @foreach ($typeLabels as $value => $label)
          <option value="{{ $value }}" {{ old('type', $record->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">日期</label>
      <input type="date" name="occurred_at" class="form-control" value="{{ old('occurred_at', $record->occurred_at->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4 mb-3">
      <label class="form-label">可見範圍</label>
      <select name="visibility" class="form-select">
        <option value="shared" {{ old('visibility', $record->visibility) === 'shared' ? 'selected' : '' }}>雙方可見</option>
        <option value="private" {{ old('visibility', $record->visibility) === 'private' ? 'selected' : '' }}>私人</option>
      </select>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">標題</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $record->title) }}" required>
  </div>

  <div class="mb-3">
    <label class="form-label">內容</label>
    <textarea name="content" class="form-control" rows="5" required>{{ old('content', $record->content) }}</textarea>
  </div>

  <button type="submit" class="btn btn-dark">儲存</button>
  <a href="{{ route('admin.records.index') }}" class="btn btn-link text-muted">取消</a>
</form>
@endsection
