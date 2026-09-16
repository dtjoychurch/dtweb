
@extends('front.layouts.app')

@section('title', '門訓歷程 ｜ 一起走一段路，記下生命被改變的痕跡')

@section('content')

<!-- ============ Hero 輪播（可在後台「首頁 Hero」管理） =========== -->
@if ($heroSlides->isNotEmpty())
  <div id="carousel" class="carousel slide carousel-fade animate__animated animate__fadeIn" data-bs-ride="carousel">
    @if ($heroSlides->count() > 1)
      <div class="carousel-indicators">
        @foreach ($heroSlides as $slide)
          <button type="button" data-bs-target="#carousel" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" @if($loop->first) aria-current="true" @endif aria-label="Slide {{ $loop->iteration }}"></button>
        @endforeach
      </div>
    @endif
    <div class="carousel-inner">
      @foreach ($heroSlides as $slide)
        <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-bs-interval="9000">
          <img src="{{ asset($slide->image_path) }}" class="d-block vw-100" alt="{{ $slide->title ?: '門訓同行' }}">
          @if ($slide->title || $slide->subtitle)
            <div class="carousel-caption d-none d-md-block">
              @if ($slide->title)
                <h1 class="fw-bold">{{ $slide->title }}</h1>
              @endif
              @if ($slide->subtitle)
                <p>{{ $slide->subtitle }}</p>
              @endif
            </div>
          @endif
        </div>
      @endforeach
    </div>
    @if ($heroSlides->count() > 1)
      <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    @endif
  </div>
@else
  <div class="position-relative">
    <img src="{{ asset('img/hero1.jpg') }}" class="d-block vw-100" alt="門訓同行" style="max-height: 100vh; object-fit: cover;">
    <div class="position-absolute top-50 start-50 translate-middle text-white text-center d-none d-md-block">
      <h1 class="fw-bold">一起走一段路，記下生命被改變的痕跡。</h1>
      <p>門訓不只是學習，而是一段彼此陪伴、一起成長的旅程。</p>
    </div>
  </div>
@endif
<!-- ============ Hero 輪播 =========== -->

<!-- ============ CTA =========== -->
<div id="trigger-point" style="height: 1px;"></div>
<div class="container pt-4 ps-5" style="max-width: 85%;">
  <div class="mt-5 mb-5 ps-5">
    <div class="d-flex justify-content-center py-2 flex-wrap">
      @auth
        <a href="{{ route('discipleship.index') }}" class="mx-3 text-decoration-none fw-semibold glow-button">我的門訓</a>
        <a href="{{ route('journey.index') }}" class="mx-3 text-decoration-none fw-semibold glow-button">生命歷程</a>
        <a href="{{ route('notes.index') }}" class="mx-3 text-decoration-none fw-semibold glow-button">我的筆記</a>
      @else
        <a href="{{ route('register') }}" class="mx-3 text-decoration-none fw-semibold glow-button">註冊</a>
        <a href="{{ route('login') }}" class="mx-3 text-decoration-none fw-semibold glow-button">登入</a>
      @endauth
      <a href="{{ route('pages.about') }}" class="mx-3 text-decoration-none fw-semibold glow-button">認識門訓</a>
      <a href="{{ route('pages.resources') }}" class="mx-3 text-decoration-none fw-semibold glow-button">資源</a>
    </div>
  </div>
<div>
<!-- ============ CTA =========== -->

<!-- ============ 門訓介紹 =========== -->
<div class="container my-5">
  <div>
    <h2 class="fw-bold text-muted m-4 pb-3">ABOUT · 門訓是什麼</h2>
  </div>
  <div class="row align-items-center">
    <div class="col-md-6 text-md-end text-center mb-4 mb-md-0">
      <img src="{{ asset('img/people.jpg') }}" alt="門訓同行" class="img-fluid rounded shadow" style="max-width: 80%;">
    </div>

    <div class="col-md-6">
      <h3 class="fw-bold text-muted mb-3">門訓，是一段真實的生命同行</h3>
      <hr>

      <h5 class="fw-bold text-muted pt-2 pb-3" style="font-size: 1.5rem; line-height: 1.6;">
        門訓不是完成幾堂課，<br>
        而是這段關係走過了什麼。<br><br>

        每一次的談話、掙扎、決定與禱告，<br>
        都是生命被塑造的痕跡。
      </h5>

      <p class="fw-bold text-muted pt-3" style="line-height: 1.6;">
        我們相信，成長發生在真實的同行裡——<br>
        有人陪你走過低谷，也有人和你一起慶祝突破。
      </p>
    </div>
  </div>
</div>
<!-- ============ 門訓介紹 =========== -->

<!-- ============ 門訓歷程 Timeline =========== -->
<div class="container my-5">
  <h2 class="fw-bold text-muted m-4 pb-3 text-center">門訓歷程</h2>

  <div class="row text-center g-4">
    @foreach ([
        ['icon' => 'bi-flag', 'title' => '開始門訓', 'desc' => '建立一段門訓關係的起點'],
        ['icon' => 'bi-people', 'title' => '第一次相遇', 'desc' => '彼此認識，打開真實的對話'],
        ['icon' => 'bi-shield-check', 'title' => '一起面對', 'desc' => '陪伴走過掙扎與困難'],
        ['icon' => 'bi-tree', 'title' => '生命成長', 'desc' => '看見改變在生活中發生'],
        ['icon' => 'bi-signpost-2', 'title' => '新的決定', 'desc' => '在門訓中做出重要的選擇'],
        ['icon' => 'bi-arrow-repeat', 'title' => '持續同行', 'desc' => '關係不斷延續、繼續前行'],
    ] as $step)
      <div class="col-6 col-md-4 col-lg-2">
        <div class="p-3">
          <i class="bi {{ $step['icon'] }} fs-1" style="color:#8c6d5a;"></i>
          <h6 class="fw-bold mt-3 mb-1">{{ $step['title'] }}</h6>
          <p class="small text-muted mb-0">{{ $step['desc'] }}</p>
        </div>
      </div>
    @endforeach
  </div>
</div>
<!-- ============ 門訓歷程 Timeline =========== -->

@if ($testimonies->isNotEmpty())
  <!-- ============ 見證分享（可在後台「見證分享」管理） =========== -->
  <div class="container my-5" id="testimonies">
    <div class="d-flex justify-content-between align-items-center mb-4 px-4 px-md-0">
      <h2 class="fw-bold text-muted mb-0">見證分享</h2>
      @if ($testimonies->count() > 1)
        <div class="d-none d-md-flex gap-2">
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle testimony-scroll-btn" data-dir="-1" aria-label="上一篇">‹</button>
          <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle testimony-scroll-btn" data-dir="1" aria-label="下一篇">›</button>
        </div>
      @endif
    </div>

    <div class="testimony-scroll" id="testimonyScroll">
      @foreach ($testimonies as $testimony)
        <a href="{{ route('testimonies.show', $testimony) }}" class="testimony-card d-block text-decoration-none text-dark">
          <div class="testimony-card-image">
            @if ($testimony->image_path)
              <img src="{{ asset($testimony->image_path) }}" alt="{{ $testimony->title }}">
            @endif
          </div>
          <div class="testimony-card-body">
            <h5 class="fw-bold">{{ $testimony->title }}</h5>
            <p class="text-muted" style="white-space: pre-line;">{{ \Illuminate\Support\Str::limit($testimony->content, 200) }}</p>
            <span class="small fw-semibold" style="color:#8c6d5a;">閱讀全文 →</span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
  <!-- ============ 見證分享 =========== -->

  <script>
    document.getElementById('testimonyScroll')?.parentElement.querySelectorAll('.testimony-scroll-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const track = document.getElementById('testimonyScroll');
        // 一次翻一整頁（目前畫面上塞得下幾張卡片，就滑動那麼多）而不是每次只滑一張。
        track.scrollBy({ left: track.clientWidth * parseInt(btn.dataset.dir, 10), behavior: 'smooth' });
      });
    });
  </script>
@endif

@auth
  <!-- ============ 最近期門訓 =========== -->
  <div class="container my-5">
    <h2 class="fw-bold text-muted m-4 pb-3">最近期門訓</h2>

    @if ($recentSessions->isEmpty())
      <p class="text-muted ms-4">目前還沒有門訓紀錄，<a href="{{ route('discipleship.index') }}">查看我的門訓 →</a></p>
    @else
      <div class="row g-4">
        @foreach ($recentSessions as $session)
          <div class="col-md-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <div class="text-muted small mb-2">{{ $session->session_date->format('Y/m/d') }}</div>
                <h5 class="card-title">{{ $session->title ?: '一次門訓' }}</h5>
                <p class="card-text text-muted">
                  {{ $session->relationship->mentor->name }} × {{ $session->relationship->disciple->name }}
                </p>
                <a href="{{ route('discipleship.sessions.show', [$session->relationship, $session]) }}" class="btn btn-outline-dark btn-sm">查看 →</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
  <!-- ============ 最近期門訓 =========== -->
@endauth

@endsection

<script>
  window.addEventListener('DOMContentLoaded', () => {
    const navlinks = document.querySelectorAll('.navlink');
    const logo = document.getElementById('nav-logo');
    const navbar = document.getElementById('mainNav');
    logo.src = "{{ asset('img/2.png') }}";
    // 先隱藏nav的連結
    navlinks.forEach(el => el.style.display = 'none');
    navbar.classList.remove('fixed-top', 'bg-white', 'shadow', 'navbar-shrink');
  });

  window.addEventListener('scroll', function () {
    const navbar = document.getElementById('mainNav');
    const logo = document.getElementById('nav-logo');
    const navlinks = document.querySelectorAll('.navlink');
    const trigger = document.getElementById('trigger-point');
    const triggerTop = trigger.getBoundingClientRect().top;

    if (triggerTop <= 0) {
      navbar.classList.add('fixed-top', 'bg-white', 'shadow', 'transition', 'navbar-shrink');
      logo.src = "{{ asset('img/3.png') }}";
      navlinks.forEach(el => el.style.display = 'block');
    } else {
      navbar.classList.remove('fixed-top', 'bg-white', 'shadow', 'navbar-shrink');
      logo.src = "{{ asset('img/2.png') }}";
      navlinks.forEach(el => el.style.display = 'none');
    }
  });
</script>
