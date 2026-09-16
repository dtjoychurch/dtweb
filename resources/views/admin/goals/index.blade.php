
@extends('admin.layouts.app')

@section('title', '門訓目標管理')

@section('content')
<h2 class="fw-bold text-muted mb-3">門訓目標</h2>

@php
  $statusLabels = ['pending' => '尚未開始', 'in_progress' => '進行中', 'completed' => '已完成', 'cancelled' => '已取消'];
@endphp

<form method="GET" class="mb-3 d-flex gap-2">
  <input type="text" name="search" class="form-control" placeholder="搜尋姓名" value="{{ request('search') }}" style="max-width: 260px;">
  <select name="status" class="form-select" onchange="this.form.submit()" style="max-width: 160px;">
    <option value="">全部狀態</option>
    @foreach ($statusLabels as $value => $label)
      <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
  </select>
  <button type="submit" class="btn btn-outline-dark">搜尋</button>
</form>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>標題</th>
        <th>狀態</th>
        <th>可見範圍</th>
        <th>門訓關係</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($goals as $goal)
        <tr>
          <td>{{ $goal->title }}</td>
          <td><span class="badge bg-light text-dark border">{{ $statusLabels[$goal->status] }}</span></td>
          <td>{{ $goal->visibility === 'shared' ? '雙方可見' : '私人' }}</td>
          <td>{{ $goal->relationship->mentor->name }} × {{ $goal->relationship->disciple->name }}</td>
          <td class="text-end">
            <a href="{{ route('admin.goals.edit', $goal) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.goals.destroy', $goal) }}" class="d-inline" onsubmit="return confirm('確定要刪除這個目標嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">沒有目標資料</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $goals->links() }}
@endsection
