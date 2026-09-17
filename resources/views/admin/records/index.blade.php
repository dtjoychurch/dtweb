
@extends('admin.layouts.app')

@section('title', '生命歷程管理')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold text-muted mb-0">生命歷程紀錄</h2>
  <a href="{{ route('admin.record-types.index') }}" class="btn btn-outline-dark btn-sm">管理紀錄類型</a>
</div>

<form method="GET" class="mb-3 d-flex gap-2 flex-wrap">
  <input type="text" name="search" class="form-control" placeholder="搜尋姓名" value="{{ request('search') }}" style="max-width: 260px;">
  <select name="type_id" class="form-select" onchange="this.form.submit()" style="max-width: 160px;">
    <option value="">全部類型</option>
    @foreach ($types as $type)
      <option value="{{ $type->id }}" {{ (string) request('type_id') === (string) $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
    @endforeach
  </select>
  <select name="relationship_id" class="form-select" onchange="this.form.submit()" style="max-width: 220px;">
    <option value="">全部門訓關係</option>
    @foreach ($relationships as $r)
      <option value="{{ $r->id }}" {{ (string) request('relationship_id') === (string) $r->id ? 'selected' : '' }}>{{ $r->mentor->name }} × {{ $r->disciple->name }}</option>
    @endforeach
  </select>
  <button type="submit" class="btn btn-outline-dark">搜尋</button>
</form>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>日期</th>
        <th>類型</th>
        <th>標題</th>
        <th>可見範圍</th>
        <th>門訓關係</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($records as $record)
        <tr>
          <td>{{ $record->occurred_at->format('Y/m/d') }}</td>
          <td><span class="badge bg-light text-dark border">{{ $record->type->name ?? '（類型已刪除）' }}</span></td>
          <td>{{ $record->title }}</td>
          <td>{{ $record->visibility === 'shared' ? '雙方可見' : '私人' }}</td>
          <td>{{ $record->relationship->mentor->name }} × {{ $record->relationship->disciple->name }}</td>
          <td class="text-end">
            <a href="{{ route('admin.records.edit', $record) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.records.destroy', $record) }}" class="d-inline" onsubmit="return confirm('確定要刪除這筆紀錄嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">沒有紀錄資料</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $records->links() }}
@endsection
