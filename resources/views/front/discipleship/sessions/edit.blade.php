
@extends('front.layouts.app')

@section('title', '編輯門訓紀錄')

@section('content')
<div class="container page-content pb-5" style="max-width: 900px;">

  <a href="{{ route('discipleship.sessions.show', [$relationship, $session]) }}" class="text-muted small text-decoration-none">← 回到這次門訓</a>

  <h2 class="fw-bold text-muted mt-2 mb-4">編輯門訓紀錄</h2>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('discipleship.sessions.update', [$relationship, $session]) }}" class="border rounded p-3">
    @csrf
    @method('PUT')

    <div class="row">
      <div class="col-md-4 mb-2">
        <label class="form-label">日期</label>
        <input type="date" name="session_date" class="form-control" value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}" required>
      </div>
      <div class="col-md-8 mb-2">
        <label class="form-label">標題（選填）</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $session->title) }}">
      </div>
    </div>

    <div class="mb-2">
      <label class="form-label">門訓內容</label>
      <textarea name="content" class="form-control" rows="6" required>{{ old('content', $session->content) }}</textarea>
    </div>

    <div class="form-check form-switch mb-3">
      <input type="hidden" name="shared" value="0">
      <input
        type="checkbox"
        name="shared"
        value="1"
        class="form-check-input"
        id="editSessionShared"
        {{ old('shared', $session->hidden_from ? '0' : '1') == '1' ? 'checked' : '' }}
      >
      <label class="form-check-label" for="editSessionShared">讓對方看到這筆門訓紀錄</label>
    </div>

    <button type="submit" class="btn btn-dark btn-sm">儲存</button>
    <a href="{{ route('discipleship.sessions.show', [$relationship, $session]) }}" class="btn btn-link btn-sm text-muted">取消</a>
  </form>
</div>
@endsection
