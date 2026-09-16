
@extends('front.layouts.app')

@section('title', '新增筆記')

@section('content')
<div class="container page-content pb-5" style="max-width: 700px;">
  <h2 class="fw-bold text-muted mb-4">寫一篇私人筆記</h2>
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

  <form method="POST" action="{{ route('notes.store') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label">關聯門訓（選填）</label>
      <select name="session_id" class="form-select">
        <option value="">不關聯特定門訓</option>
        @foreach ($sessions as $session)
          <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
            {{ $session->session_date->format('Y/m/d') }}｜{{ $session->relationship->mentor->name }} × {{ $session->relationship->disciple->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">標題（選填）</label>
      <input type="text" name="title" class="form-control" value="{{ old('title') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">內容</label>
      <textarea name="content" class="form-control" rows="8" required>{{ old('content') }}</textarea>
    </div>

    <button type="submit" class="btn btn-dark">儲存筆記</button>
    <a href="{{ route('notes.index') }}" class="btn btn-link text-muted">取消</a>
  </form>
</div>
@endsection
