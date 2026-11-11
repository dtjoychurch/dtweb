
@extends('front.layouts.app')

@section('title', '我的門訓')

@section('content')
<div class="container page-content pb-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-muted mb-0">我的門訓</h2>
    <a href="{{ route('discipleship.invites.index') }}" class="btn btn-dark btn-sm">＋ 申請門訓關係</a>
  </div>
  <hr>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($relationships->isEmpty())
    <p class="text-muted mt-4">目前還沒有門訓關係。可以點上面「申請門訓關係」自己發起，或請管理員為您建立。</p>
  @else
    <div class="row g-4 mt-1">
      @foreach ($relationships as $relationship)
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body d-flex flex-column">
              <div class="text-muted small mb-1">
                {{ $relationship->role_label === 'mentor' ? 'Disciple' : 'Mentor' }}
              </div>
              <h5 class="card-title fw-bold">{{ $relationship->counterpart?->name ?? '（使用者已刪除）' }}</h5>

              <div class="small text-muted mb-1">
                開始於 {{ $relationship->started_at->format('Y/m/d') }}
                （已同行 {{ (int) $relationship->started_at->diffInDays(now()) }} 天）
              </div>

              @if ($relationship->approval_status === 'pending')
                <span class="badge bg-warning text-dark mb-3" style="width:fit-content;">等待管理員審核</span>
              @elseif ($relationship->approval_status === 'rejected')
                <span class="badge bg-danger mb-3" style="width:fit-content;">申請已被拒絕</span>
              @else
                <span class="badge {{ $relationship->status === 'active' ? 'bg-success' : ($relationship->status === 'paused' ? 'bg-secondary' : 'bg-dark') }} mb-3" style="width:fit-content;">
                  {{ ['active' => '進行中', 'paused' => '暫停中', 'completed' => '已完成'][$relationship->status] }}
                </span>
              @endif

              <div class="mt-auto">
                <div class="small text-muted mb-2">
                  最近門訓
                  {{ $relationship->latest_session?->session_date->format('Y/m/d') ?? '尚無紀錄' }}
                </div>
                <a href="{{ route('discipleship.show', $relationship) }}" class="btn btn-outline-dark btn-sm">查看門訓歷程 →</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
