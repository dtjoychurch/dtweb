
@extends('front.layouts.app')

@section('title', '生命歷程')

@section('content')
<div class="container page-content pb-5" style="max-width: 800px;">
  <h2 class="fw-bold text-muted mb-4">生命歷程</h2>
  <hr>

  @php
    // 類型現在是後台可管理的表，不是固定 8 種，所以顏色改用固定色盤依 type id 循環對應，
    // 而不是寫死每個 slug 的顏色（不然新增的類型會沒有顏色可用）。
    $typePalette = ['#6b8e5a', '#b06a5a', '#5a7c8e', '#8c6d5a', '#c99a3a', '#7a7a7a', '#5a5a8e', '#a35ab0'];
  @endphp

  @if ($records->isEmpty())
    <p class="text-muted mt-4">目前還沒有生命歷程紀錄。可以到門訓歷程頁面新增。</p>
  @else
    <div class="journey-timeline ps-3" style="border-left: 2px solid #e5ded7;">
      @foreach ($records as $record)
        @php $typeColor = $typePalette[$record->type_id % count($typePalette)]; @endphp
        <div class="mb-4 ps-3" style="position: relative;">
          <span style="position:absolute; left:-1.55rem; top:0.3rem; width:10px; height:10px; border-radius:50%; background:{{ $typeColor }};"></span>

          <div class="text-muted small mb-1">{{ $record->occurred_at->format('Y/m/d') }}</div>
          <div class="mb-1">
            <span class="fw-bold" style="color:{{ $typeColor }};">● {{ $record->type->name }}</span>
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
