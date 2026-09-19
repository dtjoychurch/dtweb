
@extends('admin.layouts.app')

@section('title', '資料庫備份還原')

@section('content')
<h2 class="fw-bold text-muted mb-3">資料庫備份還原</h2>
<p class="text-muted small">每天自動備份一次，只保留最近 30 份。還原會覆蓋現在的資料，執行前系統會先自動備份目前的狀態，萬一選錯還能救回來。</p>

@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="table-responsive">
  <table class="table bg-white shadow-sm align-middle">
    <thead>
      <tr>
        <th>備份時間</th>
        <th>檔案</th>
        <th>大小</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($backups as $backup)
        <tr>
          <td>{{ $backup['last_modified']->format('Y/m/d H:i') }}</td>
          <td><code>{{ $backup['filename'] }}</code></td>
          <td>{{ number_format($backup['size'] / 1024, 1) }} KB</td>
          <td class="text-end">
            <form
              method="POST"
              action="{{ route('admin.database-backups.restore', $backup['filename']) }}"
              onsubmit="return confirm('確定要用這份備份（{{ $backup['filename'] }}）覆蓋現在的資料庫嗎？\n\n這個動作無法復原，執行前系統會先自動備份目前的狀態。');"
            >
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-danger">還原這份</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="text-center text-muted py-4">還沒有任何備份</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
