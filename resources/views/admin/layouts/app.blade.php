<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" href="{{ asset('farmily_icon.ico') }}" type="image/x-icon">
  <title>@yield('title', '後台管理')｜門訓歷程</title>

  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/layout.css">

  <style>
    body { background-color: #f7f5f2; }
    .admin-sidebar {
      width: 240px;
      min-height: 100vh;
      background-color: #fff;
      border-right: 1px solid #eee;
    }
    .admin-sidebar .brand {
      font-family: 'Noto Serif TC', serif;
      color: #8c6d5a;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .admin-sidebar .nav-link {
      color: #555;
      border-radius: 8px;
    }
    .admin-sidebar .nav-link.active,
    .admin-sidebar .nav-link:hover {
      background-color: #f1ece7;
      color: #8c6d5a;
    }
    .admin-sidebar .nav-section {
      font-size: 0.75rem;
      color: #aaa;
      text-transform: uppercase;
      margin: 1rem 0 0.25rem 0.75rem;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <aside class="admin-sidebar p-3 d-none d-md-block">
      <a href="{{ route('admin.dashboard') }}" class="brand text-decoration-none d-block mb-4">門訓歷程．後台</a>

      <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
          <i class="bi bi-speedometer2 me-1"></i> Dashboard
        </a>

        <div class="nav-section">資料管理</div>
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">使用者</a>
        <a class="nav-link {{ request()->routeIs('admin.relationships.*') ? 'active' : '' }}" href="{{ route('admin.relationships.index') }}">門訓關係</a>
        <a class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}" href="{{ route('admin.sessions.index') }}">門訓紀錄</a>
        <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">留言</a>
        <a class="nav-link {{ request()->routeIs('admin.feedbacks.*') ? 'active' : '' }}" href="{{ route('admin.feedbacks.index') }}">意見信箱</a>
        <a class="nav-link {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}" href="{{ route('admin.notes.index') }}">筆記</a>
        <a class="nav-link {{ request()->routeIs('admin.records.*') ? 'active' : '' }}" href="{{ route('admin.records.index') }}">生命歷程</a>
        <a class="nav-link {{ request()->routeIs('admin.goals.*') ? 'active' : '' }}" href="{{ route('admin.goals.index') }}">門訓目標</a>

        <div class="nav-section">內容管理</div>
        <a class="nav-link {{ request()->routeIs('admin.hero-slides.*') ? 'active' : '' }}" href="{{ route('admin.hero-slides.index') }}">首頁 Hero</a>
        <a class="nav-link {{ request()->routeIs('admin.testimonies.*') ? 'active' : '' }}" href="{{ route('admin.testimonies.index') }}">見證分享</a>

        <div class="nav-section">&nbsp;</div>
        <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-box-arrow-left me-1"></i> 回到前台</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
            <i class="bi bi-box-arrow-right me-1"></i> 登出
          </button>
        </form>
      </nav>
    </aside>

    <main class="flex-grow-1 p-4">
      <div class="d-flex d-md-none justify-content-between align-items-center mb-3">
        <span class="brand" style="font-family:'Noto Serif TC', serif; color:#8c6d5a; font-weight:bold;">門訓歷程．後台</span>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-dark">選單</a>
      </div>

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

      @yield('content')
    </main>
  </div>
</body>
</html>
