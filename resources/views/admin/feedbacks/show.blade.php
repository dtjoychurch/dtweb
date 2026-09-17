
@extends('admin.layouts.app')

@section('title', '意見詳情')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold text-muted mb-0">意見詳情</h2>
  <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-outline-dark btn-sm">返回列表</a>
</div>

<div class="card bg-white shadow-sm" style="max-width: 700px;">
  <div class="card-body">
    <div class="text-muted small mb-2">
      {{ $feedback->user->name }}（{{ $feedback->user->email }}）｜{{ $feedback->created_at->format('Y/m/d H:i') }}
    </div>
    <h4 class="fw-bold mb-3">{{ $feedback->title }}</h4>
    <p class="mb-0" style="white-space: pre-wrap;">{{ $feedback->content }}</p>
  </div>
</div>

<form method="POST" action="{{ route('admin.feedbacks.destroy', $feedback) }}" class="mt-3" onsubmit="return confirm('確定要刪除這則意見嗎？');">
  @csrf
  @method('DELETE')
  <button type="submit" class="btn btn-outline-danger btn-sm">刪除這則意見</button>
</form>
@endsection
