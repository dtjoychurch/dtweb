
@extends('front.layouts.app')

@section('title', '生命歷程')

@section('content')
<div class="container page-content pb-5" style="max-width: 800px;">
  <h2 class="fw-bold text-muted mb-4">生命歷程</h2>
  <hr>

  @php
    $typeLabels = ['growth'=>'成長','struggle'=>'掙扎','reflection'=>'反思','prayer'=>'禱告','milestone'=>'里程碑','observation'=>'觀察','decision'=>'決定','testimony'=>'見證'];
    $typeColors = [
        'growth' => '#6b8e5a', 'struggle' => '#b06a5a', 'reflection' => '#5a7c8e',
        'prayer' => '#8c6d5a', 'milestone' => '#c99a3a', 'observation' => '#7a7a7a',
        'decision' => '#5a5a8e', 'testimony' => '#a35ab0',
    ];
  @endphp

  @if ($records->isEmpty())
    <p class="text-muted mt-4">目前還沒有生命歷程紀錄。可以到門訓歷程頁面新增。</p>
  @else
    <div class="journey-timeline ps-3" style="border-left: 2px solid #e5ded7;">
      @foreach ($records as $record)
        <div class="mb-4 ps-3" style="position: relative;">
          <span style="position:absolute; left:-1.55rem; top:0.3rem; width:10px; height:10px; border-radius:50%; background:{{ $typeColors[$record->type] }};"></span>

          <div class="text-muted small mb-1">{{ $record->occurred_at->format('Y/m/d') }}</div>
          <div class="mb-1">
            <span class="fw-bold" style="color:{{ $typeColors[$record->type] }};">● {{ $typeLabels[$record->type] }}</span>
            @if ($record->visibility === 'private')
              <span class="badge bg-light text-dark border ms-1">私人</span>
            @endif
          </div>
          <h6 class="mb-1">{{ $record->title }}</h6>
          <p class="text-muted mb-1">{{ \Illuminate\Support\Str::limit(strip_tags($record->content), 140) }}</p>
          <div class="text-muted small">
            {{ $record->relationship->mentor->name }} × {{ $record->relationship->disciple->name }}
            ·
            <a href="{{ route('discipleship.show', $record->relationship) }}" class="text-decoration-none">查看門訓關係 →</a>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
