
@extends('front.layouts.app')

@section('title', '登入')

@section('content')
<div class="container page-content pb-5 d-flex justify-content-center">
  <div style="max-width: 420px; width: 100%;">
    <h2 class="fw-bold text-muted mb-1">登入</h2>
    <p class="text-muted">歡迎回來，繼續你的門訓歷程。</p>
    <hr>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-4">
      @csrf

      <div class="mb-3">
        <label for="email" class="form-label">電子信箱</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="請輸入 Email" required autofocus>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">密碼</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="請輸入密碼" required>
      </div>

      <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="remember" id="remember">
        <label class="form-check-label" for="remember">記住我</label>
      </div>

      <button type="submit" class="btn btn-dark w-100 mb-4">登入</button>

      <div class="text-center">
        尚未有帳號？<a class="ms-2 text-dark" href="{{ route('register') }}">註冊</a>
      </div>
    </form>
  </div>
</div>
@endsection
