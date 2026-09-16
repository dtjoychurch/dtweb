
@extends('front.layouts.app')

@section('title', '資源')

@section('content')
<div class="container page-content pb-5" style="max-width: 800px;">
  <h2 class="fw-bold text-muted mb-4">資源</h2>
  <hr>
  <p class="text-muted mb-4">給 mentor 與 disciple 的一些門訓提醒與引導。</p>

  <div class="list-group mb-5">
    @foreach ([
        ['title' => '如何展開第一次門訓', 'desc' => '從認識彼此的故事開始，建立信任與安全感。'],
        ['title' => '陪伴掙扎中的人', 'desc' => '不急著給答案，先學習聆聽與同理。'],
        ['title' => '設定門訓目標', 'desc' => '一起討論這段關係想要走向的方向。'],
        ['title' => '持續同行的秘訣', 'desc' => '穩定的頻率，比長度更重要。'],
    ] as $item)
      <div class="list-group-item">
        <h6 class="fw-bold mb-1">{{ $item['title'] }}</h6>
        <p class="text-muted mb-0">{{ $item['desc'] }}</p>
      </div>
    @endforeach
  </div>

  <!-- ============ 門徒概要 15 點 =========== -->
  <h3 class="fw-bold text-muted mb-3">門徒概要 15 點</h3>

  <div class="accordion discipleship-accordion" id="discipleProfileAccordion">
    @foreach ($discipleProfilePoints as $index => $point)
      <div class="accordion-item">
        <h2 class="accordion-header">
          <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#point{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="point{{ $index }}">
            <span class="fw-bold me-2">{{ $index + 1 }}.</span> {{ $point['title'] }}
          </button>
        </h2>
        <div id="point{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#discipleProfileAccordion">
          <div class="accordion-body text-muted">
            <p class="mb-2">{{ $point['content'] }}</p>
            <a href="{{ $point['url'] }}" target="_blank" rel="noopener" class="small fw-semibold text-decoration-none" style="color:#8c6d5a;">
              詳細內容 ↗
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <!-- ============ 門徒概要 15 點 =========== -->
</div>
@endsection
