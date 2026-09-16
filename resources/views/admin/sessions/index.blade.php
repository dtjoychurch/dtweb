
@extends('admin.layouts.app')

@section('title', '門訓紀錄管理')

@section('content')
<h2 class="fw-bold text-muted mb-3">門訓紀錄</h2>

<form method="GET" class="mb-3 d-flex gap-2">
  <input type="text" name="search" class="form-control" placeholder="搜尋姓名" value="{{ request('search') }}" style="max-width: 260px;">
  <select name="relationship_id" class="form-select" onchange="this.form.submit()" style="max-width: 160px;">
    <option value="">全部門訓關係</option>
    @foreach ($relationships as $r)
      <option value="{{ $r->id }}" {{ (string) request('relationship_id') === (string) $r->id ? 'selected' : '' }}>
        {{ $r->mentor->name }} × {{ $r->disciple->name }}
      </option>
    @endforeach
  </select>
  <button type="submit" class="btn btn-outline-dark">搜尋</button>
</form>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>日期</th>
        <th>門訓關係</th>
        <th>標題</th>
        <th>記錄者</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($sessions as $session)
        <tr>
          <td>{{ $session->session_date->format('Y/m/d') }}</td>
          <td>{{ $session->relationship->mentor->name }} × {{ $session->relationship->disciple->name }}</td>
          <td>{{ $session->title ?: '—' }}</td>
          <td>{{ $session->creator->name }}</td>
          <td class="text-end">
            <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.sessions.destroy', $session) }}" class="d-inline" onsubmit="return confirm('刪除後歷史紀錄仍會保留，確定要刪除嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">沒有門訓紀錄</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $sessions->links() }}
@endsection
