
@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2 class="fw-bold text-muted mb-4">Dashboard</h2>

<div class="row g-3">
  @foreach ([
      ['label' => '使用者數', 'value' => $stats['users']],
      ['label' => '門訓關係數', 'value' => $stats['relationships']],
      ['label' => '進行中的門訓', 'value' => $stats['active_relationships']],
      ['label' => '本月門訓次數', 'value' => $stats['sessions_this_month']],
      ['label' => '生命歷程紀錄數', 'value' => $stats['records']],
  ] as $stat)
    <div class="col-6 col-md-4 col-xl">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">{{ $stat['label'] }}</div>
          <div class="fs-3 fw-bold" style="color:#8c6d5a;">{{ $stat['value'] }}</div>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
