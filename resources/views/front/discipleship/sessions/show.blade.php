
@extends('front.layouts.app')

@section('title', '第 '.$ordinal.' 次門訓')

@section('content')
<div class="container page-content pb-5" style="max-width: 900px;">

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center">
    <a href="{{ route('discipleship.show', $relationship) }}" class="text-muted small text-decoration-none">← 回到門訓歷程</a>

    @can('delete', $session)
      <form method="POST" action="{{ route('discipleship.sessions.destroy', [$relationship, $session]) }}" onsubmit="return confirm('確定要刪除這次門訓紀錄嗎？（刪除後仍會保留在資料庫中，可聯絡管理員還原）');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-link text-danger p-0">刪除這次門訓紀錄</button>
      </form>
    @endcan
  </div>

  <h2 class="fw-bold text-muted mt-2 mb-1">第 {{ $ordinal }} 次門訓</h2>
  <div class="text-muted mb-4">{{ $session->session_date->format('Y/m/d') }}</div>

  @if ($session->title)
    <h4 class="mb-3">{{ $session->title }}</h4>
  @endif

  <!-- ============ 門訓內容 =========== -->
  <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">門訓內容</h5>
  <p style="white-space: pre-line;">{{ $session->content }}</p>
  <div class="text-muted small mb-4">記錄者：{{ $session->creator->name }}</div>
  <!-- ============ 門訓內容 =========== -->

  <!-- ============ 照片 =========== -->
  <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">照片</h5>

  @if ($session->photos->isNotEmpty())
    <div class="row g-3 mb-3">
      @foreach ($session->photos as $photo)
        <div class="col-6 col-md-3">
          <div class="position-relative">
            <a href="{{ asset($photo->path) }}" target="_blank" rel="noopener">
              <img src="{{ asset($photo->path) }}" alt="門訓照片" class="rounded shadow-sm" style="width:100%; height:140px; object-fit:cover;">
            </a>
            @can('delete', $photo)
              <form method="POST" action="{{ route('discipleship.sessions.photos.destroy', [$relationship, $session, $photo]) }}" onsubmit="return confirm('確定要刪除這張照片嗎？');" class="position-absolute top-0 end-0 m-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger py-0 px-1" style="line-height: 1.4;">✕</button>
              </form>
            @endcan
          </div>
        </div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('discipleship.sessions.photos.store', [$relationship, $session]) }}" enctype="multipart/form-data" class="mb-5">
    @csrf
    <div class="input-group input-group-sm" style="max-width: 420px;">
      <input type="file" name="photos[]" class="form-control" accept="image/*" multiple required>
      <button type="submit" class="btn btn-outline-dark">新增照片</button>
    </div>
  </form>
  <!-- ============ 照片 =========== -->

  <!-- ============ 我的私人筆記 =========== -->
  <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">我的私人筆記</h5>
  <p class="text-muted small">只有自己看得到</p>

  <form method="POST" action="{{ $note ? route('notes.update', $note) : route('notes.store') }}" class="mb-5">
    @csrf
    @if ($note) @method('PUT') @endif
    <input type="hidden" name="session_id" value="{{ $session->id }}">
    <textarea name="content" class="form-control mb-2" rows="4" placeholder="寫下這次門訓後的想法……">{{ old('content', $note->content ?? '') }}</textarea>
    <button type="submit" class="btn btn-outline-dark btn-sm">{{ $note ? '更新筆記' : '儲存筆記' }}</button>
  </form>
  <!-- ============ 我的私人筆記 =========== -->

  <!-- ============ 留言 =========== -->
  <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">留言</h5>

  @forelse ($session->comments as $comment)
    <div class="mb-3 pb-3 border-bottom">
      <div class="fw-semibold">{{ $comment->user->name }}</div>
      <div class="text-muted small mb-1">{{ $comment->created_at->format('Y/m/d H:i') }}</div>
      <p class="mb-1" style="white-space: pre-line;">{{ $comment->content }}</p>
      @if ($comment->user_id === auth()->id())
        <form method="POST" action="{{ route('discipleship.sessions.comments.destroy', [$relationship, $session, $comment]) }}" onsubmit="return confirm('確定要刪除這則留言嗎？');" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-link text-danger p-0">刪除</button>
        </form>
      @endif
    </div>
  @empty
    <p class="text-muted">目前還沒有留言。</p>
  @endforelse

  <form method="POST" action="{{ route('discipleship.sessions.comments.store', [$relationship, $session]) }}" class="mt-4">
    @csrf
    <textarea name="content" class="form-control mb-2" rows="3" placeholder="新增留言……" required></textarea>
    <button type="submit" class="btn btn-dark btn-sm">送出留言</button>
  </form>
  <!-- ============ 留言 =========== -->

</div>
@endsection
