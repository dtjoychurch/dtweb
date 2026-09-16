
@extends('admin.layouts.app')

@section('title', '留言管理')

@section('content')
<h2 class="fw-bold text-muted mb-3">留言</h2>

<form method="GET" class="mb-3">
  <input type="text" name="search" class="form-control" placeholder="搜尋使用者姓名" value="{{ request('search') }}" style="max-width: 260px;">
</form>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>時間</th>
        <th>使用者</th>
        <th>門訓關係</th>
        <th>內容</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($comments as $comment)
        <tr>
          <td>{{ $comment->created_at->format('Y/m/d H:i') }}</td>
          <td>{{ $comment->user->name }}</td>
          <td>{{ $comment->session->relationship->mentor->name }} × {{ $comment->session->relationship->disciple->name }}</td>
          <td>{{ \Illuminate\Support\Str::limit($comment->content, 40) }}</td>
          <td class="text-end">
            <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" onsubmit="return confirm('確定要刪除這則留言嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">沒有留言資料</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $comments->links() }}
@endsection
