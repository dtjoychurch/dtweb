
@extends('admin.layouts.app')

@section('title', '見證分享管理')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold text-muted mb-0">見證分享</h2>
  <a href="{{ route('admin.testimonies.create') }}" class="btn btn-dark btn-sm">＋ 新增見證</a>
</div>
<p class="text-muted small">依「排序」由小到大顯示在首頁；停用的見證不會出現在前台。</p>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>預覽</th>
        <th>標題</th>
        <th>排序</th>
        <th>狀態</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($testimonies as $testimony)
        <tr>
          <td>
            @if ($testimony->image_path)
              <img src="{{ asset($testimony->image_path) }}" alt="" style="width:80px; height:56px; object-fit:cover; border-radius:4px;">
            @else
              <span class="text-muted small">無圖片</span>
            @endif
          </td>
          <td>{{ $testimony->title }}</td>
          <td>{{ $testimony->sort_order }}</td>
          <td>
            <span class="badge {{ $testimony->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $testimony->is_active ? '啟用中' : '已停用' }}
            </span>
          </td>
          <td class="text-end">
            <a href="{{ route('testimonies.show', $testimony) }}" target="_blank" class="btn btn-sm btn-outline-secondary">查看</a>
            <a href="{{ route('admin.testimonies.edit', $testimony) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.testimonies.destroy', $testimony) }}" class="d-inline" onsubmit="return confirm('確定要刪除這篇見證嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">還沒有見證分享</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
