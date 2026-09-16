
@extends('admin.layouts.app')

@section('title', '編輯門訓紀錄')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯門訓紀錄</h2>
<p class="text-muted">{{ $session->relationship->mentor->name }} × {{ $session->relationship->disciple->name }}</p>

<form method="POST" action="{{ route('admin.sessions.update', $session) }}" style="max-width: 600px;">
  @csrf
  @method('PUT')

  <div class="row">
    <div class="col-md-4 mb-3">
      <label class="form-label">日期</label>
      <input type="date" name="session_date" class="form-control" value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-8 mb-3">
      <label class="form-label">標題</label>
      <input type="text" name="title" class="form-control" value="{{ old('title', $session->title) }}">
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">內容</label>
    <textarea name="content" class="form-control" rows="6" required>{{ old('content', $session->content) }}</textarea>
  </div>

  <button type="submit" class="btn btn-dark">儲存</button>
  <a href="{{ route('admin.sessions.index') }}" class="btn btn-link text-muted">取消</a>
</form>
@endsection
