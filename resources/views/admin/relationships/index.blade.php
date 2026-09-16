
@extends('admin.layouts.app')

@section('title', '門訓關係管理')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold text-muted mb-0">門訓關係</h2>
  <a href="{{ route('admin.relationships.create') }}" class="btn btn-dark btn-sm">＋ 新增門訓關係</a>
</div>

<form method="GET" class="mb-3">
  <input type="text" name="search" class="form-control" placeholder="搜尋姓名" value="{{ request('search') }}" style="max-width: 260px;">
</form>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>Mentor</th>
        <th>Disciple</th>
        <th>開始日期</th>
        <th>狀態</th>
        <th>門訓次數</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($relationships as $relationship)
        <tr>
          <td>{{ $relationship->mentor->name }}</td>
          <td>{{ $relationship->disciple->name }}</td>
          <td>{{ $relationship->started_at->format('Y/m/d') }}</td>
          <td>
            <span class="badge {{ $relationship->status === 'active' ? 'bg-success' : ($relationship->status === 'paused' ? 'bg-secondary' : 'bg-dark') }}">
              {{ ['active' => '進行中', 'paused' => '暫停中', 'completed' => '已完成'][$relationship->status] }}
            </span>
          </td>
          <td>{{ $relationship->sessions_count }}</td>
          <td class="text-end">
            <a href="{{ route('admin.relationships.edit', $relationship) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.relationships.destroy', $relationship) }}" class="d-inline" onsubmit="return confirm('刪除後歷史紀錄仍會保留，確定要刪除嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">沒有門訓關係資料</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $relationships->links() }}
@endsection
