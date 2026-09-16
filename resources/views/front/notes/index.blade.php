
@extends('front.layouts.app')

@section('title', '我的筆記')

@section('content')
<div class="container page-content pb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-muted mb-0">我的筆記</h2>
    <a href="{{ route('notes.create') }}" class="btn btn-dark btn-sm">＋ 新增筆記</a>
  </div>
  <hr>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <p class="text-muted small">只有你自己看得到這些筆記。</p>

  @if ($notes->isEmpty())
    <p class="text-muted mt-4">還沒有任何筆記。</p>
  @else
    <div class="list-group mt-3">
      @foreach ($notes as $note)
        <div class="list-group-item">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="text-muted small mb-1">{{ $note->created_at->format('Y/m/d') }}</div>
              <h6 class="mb-1">{{ $note->title ?: \Illuminate\Support\Str::limit(strip_tags($note->content), 30) }}</h6>
              @if ($note->session)
                <div class="text-muted small">
                  關聯門訓：{{ $note->session->relationship->mentor->name }} × {{ $note->session->relationship->disciple->name }}
                  （{{ $note->session->session_date->format('Y/m/d') }}）
                </div>
              @endif
              <p class="mb-0 mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($note->content), 100) }}</p>
            </div>
            <div class="d-flex flex-column gap-1 ms-3">
              <a href="{{ route('notes.edit', $note) }}" class="btn btn-sm btn-outline-dark">編輯</a>
              <form method="POST" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('確定要刪除這篇筆記嗎？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-link text-danger p-0">刪除</button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $notes->links() }}
    </div>
  @endif
</div>
@endsection
