<nav class="navbar bg-white shadow navbar-shrink" id="mainNav">
  <div class="container pt-4 ps-4" style="max-width: 90%;">
    <!-- LOGO -->
    <a class="navbar-brand" href="{{ route('home') }}">
      <span class="logo-crop">
        <img id="nav-logo" src="{{ asset('img/3.png') }}" alt="門訓歷程 Logo">
      </span>
    </a>

    <div class="d-none d-lg-flex align-items-center">
      <a href="{{ route('home') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">首頁</a>
      @auth
        <a href="{{ route('discipleship.index') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">門訓</a>
        <a href="{{ route('journey.index') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">生命歷程</a>
        <a href="{{ route('notes.index') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">我的筆記</a>
      @endauth
      <a href="{{ route('pages.resources') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">資源</a>
      <a href="{{ route('pages.about') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">關於</a>

      @auth
        @if (auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">後台</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="navlink mx-3 border-0 bg-transparent text-decoration-none fw-semibold glow-button">登出（{{ auth()->user()->name }}）</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">登入</a>
        <a href="{{ route('register') }}" class="navlink mx-3 text-decoration-none fw-semibold glow-button">註冊</a>
      @endauth
    </div>

    <!-- 漢堡按鈕 -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Offcanvas 主體 -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel"></h5>
        <button type="button" class="btn-close text-reset mt-2 me-2" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <hr class="mx-3 navHr">

      <div class="offcanvas-body ms-2">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}">首頁</a>
          </li>
          @auth
            <li class="nav-item">
              <a class="nav-link" href="{{ route('discipleship.index') }}">門訓</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('journey.index') }}">生命歷程</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('notes.index') }}">我的筆記</a>
            </li>
          @endauth
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages.resources') }}">資源</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('pages.about') }}">關於</a>
          </li>

          @auth
            @if (auth()->user()->isAdmin())
              <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">後台</a>
              </li>
            @endif
            <li class="nav-item">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">登出（{{ auth()->user()->name }}）</button>
              </form>
            </li>
          @else
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">登入</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">註冊</a>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </div>
</nav>

<script>
  let lastScrollTop = 0;
  const navbar = document.getElementById('mainNav');

  window.addEventListener('scroll', function () {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > lastScrollTop) {
      // 往下滑：隱藏 navbar（但保留 DOM 空間）
      navbar.classList.add("hide-navbar");
    } else {
      // 往上滑：顯示 navbar
      navbar.classList.remove("hide-navbar");
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
</script>
