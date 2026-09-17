
@extends('admin.layouts.app')

@section('title', '使用者管理')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="fw-bold text-muted mb-0">使用者</h2>
  <a href="{{ route('admin.users.create') }}" class="btn btn-dark btn-sm">＋ 新增使用者</a>
</div>

<form method="GET" class="mb-3" style="max-width: 320px;">
  <input type="text" name="search" class="form-control" placeholder="搜尋姓名或 Email" value="{{ request('search') }}">
</form>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>姓名</th>
        <th>Email</th>
        <th>角色</th>
        <th>狀態</th>
        <th>最後登入</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($users as $user)
        <tr>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            <span class="badge {{ $user->role === 'admin' ? 'bg-dark' : 'bg-secondary' }}">
              {{ $user->role === 'admin' ? 'Admin' : 'Member' }}
            </span>
          </td>
          <td>
            <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
              {{ $user->status === 'active' ? '啟用' : '停用' }}
            </span>
          </td>
          <td class="text-muted small">
            @if ($user->last_login_at)
              {{ $user->last_login_at->format('Y/m/d H:i') }}
            @else
              從未登入
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-dark">編輯</a>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('確定要刪除這個使用者嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-4">沒有使用者資料</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $users->links() }}
@endsection
