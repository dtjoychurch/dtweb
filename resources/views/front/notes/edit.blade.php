
@extends('front.layouts.app')

@section('title', '編輯筆記')

@section('content')
<div class="container page-content pb-5" style="max-width: 700px;">
  <h2 class="fw-bold text-muted mb-4">編輯筆記</h2>
  <hr>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('notes.update', $note) }}">
    @csrf
    @method('PUT')

    @if ($note->session_id)
      <input type="hidden" name="session_id" value="{{ $note->session_id }}">
    @endif

    <div class="mb-3">
      <label class="form-label">標題（選填）</label>
      <input type="text" name="title" class="form-control" value="{{ old('title', $note->title) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">內容</label>
      <textarea name="content" class="form-control" rows="8" required>{{ old('content', $note->content) }}</textarea>
    </div>

    <button type="submit" class="btn btn-dark">更新筆記</button>
    <a href="{{ route('notes.index') }}" class="btn btn-link text-muted">取消</a>
  </form>
</div>
@endsection
