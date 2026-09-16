
@extends('front.layouts.app')

@section('title', '註冊')

@section('content')
<div class="container page-content pb-5 d-flex justify-content-center">
  <div style="max-width: 420px; width: 100%;">
    <h2 class="fw-bold text-muted mb-1">開始門訓</h2>
    <p class="text-muted">建立帳號，開始記錄你的生命歷程。</p>
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

    <form method="POST" action="{{ route('register') }}" class="mt-4">
      @csrf

      <div class="mb-3">
        <label for="name" class="form-label">姓名</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">電子信箱</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">密碼</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>

      <div class="mb-4">
        <label for="password_confirmation" class="form-label">確認密碼</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
      </div>

      <button type="submit" class="btn btn-dark w-100 mb-4">註冊</button>

      <div class="text-center">
        已經有帳號？<a class="ms-2 text-dark" href="{{ route('login') }}">登入</a>
      </div>
    </form>
  </div>
</div>
@endsection
