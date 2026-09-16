
@extends('admin.layouts.app')

@section('title', '首頁 Hero 管理')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold text-muted mb-0">首頁 Hero</h2>
  <a href="{{ route('admin.hero-slides.create') }}" class="btn btn-dark btn-sm">＋ 新增 Hero 圖片</a>
</div>
<p class="text-muted small">依「排序」由小到大顯示在首頁輪播；停用的圖片不會出現在前台。</p>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>預覽</th>
        <th>標題</th>
        <th>副標</th>
        <th>排序</th>
        <th>狀態</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($heroSlides as $slide)
        <tr>
          <td><img src="{{ asset($slide->image_path) }}" alt="" style="width:100px; height:56px; object-fit:cover; border-radius:4px;"></td>
          <td>{{ $slide->title ?: '—' }}</td>
          <td>{{ $slide->subtitle ?: '—' }}</td>
          <td>{{ $slide->sort_order }}</td>
          <td>
            <span class="badge {{ $slide->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $slide->is_active ? '啟用中' : '已停用' }}
            </span>
          </td>
          <td class="text-end">
            <a href="{{ route('admin.hero-slides.edit', $slide) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}" class="d-inline" onsubmit="return confirm('確定要刪除這張 Hero 圖片嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">還沒有 Hero 圖片</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
