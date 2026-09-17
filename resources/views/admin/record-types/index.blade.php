
@extends('admin.layouts.app')

@section('title', '生命歷程類型管理')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold text-muted mb-0">生命歷程紀錄類型</h2>
  <a href="{{ route('admin.record-types.create') }}" class="btn btn-dark btn-sm">＋ 新增類型</a>
</div>
<p class="text-muted small">依「排序」由小到大顯示；停用的類型不會出現在前台新增紀錄的下拉選單裡，但既有紀錄不受影響。</p>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>名稱</th>
        <th>代碼</th>
        <th>排序</th>
        <th>使用中筆數</th>
        <th>狀態</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($recordTypes as $type)
        <tr>
          <td>{{ $type->name }}</td>
          <td><code>{{ $type->slug }}</code></td>
          <td>{{ $type->sort_order }}</td>
          <td>{{ $type->records_count }}</td>
          <td>
            <span class="badge {{ $type->is_active ? 'bg-success' : 'bg-secondary' }}">
              {{ $type->is_active ? '啟用中' : '已停用' }}
            </span>
          </td>
          <td class="text-end">
            <a href="{{ route('admin.record-types.edit', $type) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.record-types.destroy', $type) }}" class="d-inline" onsubmit="return confirm('確定要刪除這個類型嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">還沒有紀錄類型</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
