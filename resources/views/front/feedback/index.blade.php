
@extends('front.layouts.app')

@section('title', '意見信箱')

@section('content')
<div class="container page-content pb-5" style="max-width: 700px;">
  <h2 class="fw-bold text-muted mb-1">意見信箱</h2>
  <p class="text-muted">有任何想法、建議或問題，歡迎留言給我們。</p>
  <hr>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('feedback.store') }}" class="mb-5">
    @csrf

    <div class="mb-3">
      <label class="form-label">標題</label>
      <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">你的意見</label>
      <textarea name="content" class="form-control" rows="6" required>{{ old('content') }}</textarea>
    </div>

    <button type="submit" class="btn btn-dark">送出</button>
  </form>

  <h5 class="fw-bold text-muted mb-3">我送出過的意見</h5>

  @if ($feedbacks->isEmpty())
    <p class="text-muted">還沒有送出過意見。</p>
  @else
    <div class="list-group">
      @foreach ($feedbacks as $feedback)
        <div class="list-group-item">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="text-muted small mb-1">{{ $feedback->created_at->format('Y/m/d H:i') }}</div>
              <h6 class="mb-1">{{ $feedback->title }}</h6>
              <p class="mb-0">{{ $feedback->content }}</p>
            </div>
            <form method="POST" action="{{ route('feedback.destroy', $feedback) }}" class="ms-3" onsubmit="return confirm('確定要刪除這則意見嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-link text-danger p-0">刪除</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $feedbacks->links() }}
    </div>
  @endif
</div>
@endsection
