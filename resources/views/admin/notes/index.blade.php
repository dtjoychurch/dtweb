
@extends('admin.layouts.app')

@section('title', '筆記管理')

@section('content')
<h2 class="fw-bold text-muted mb-2">筆記</h2>
<p class="text-muted small">為保護隱私，管理員無法檢視筆記內容，僅能查看基本資訊並於必要時刪除。</p>

<form method="GET" class="mb-3">
  <input type="text" name="search" class="form-control" placeholder="搜尋使用者姓名" value="{{ request('search') }}" style="max-width: 260px;">
</form>

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>建立時間</th>
        <th>使用者</th>
        <th>標題</th>
        <th>關聯門訓</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($notes as $note)
        <tr>
          <td>{{ $note->created_at->format('Y/m/d H:i') }}</td>
          <td>{{ $note->user->name }}</td>
          <td>{{ $note->title ?: '（無標題）' }}</td>
          <td>{{ $note->session ? $note->session->session_date->format('Y/m/d') : '—' }}</td>
          <td class="text-end">
            <form method="POST" action="{{ route('admin.notes.destroy', $note) }}" onsubmit="return confirm('確定要刪除這篇筆記嗎？（內容無法檢視）');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">刪除</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="text-center text-muted py-4">沒有筆記資料</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{ $notes->links() }}
@endsection
