
@extends('front.layouts.app')

@section('title', '忘記密碼')

@section('content')
<div class="container page-content pb-5 d-flex justify-content-center">
  <div style="max-width: 420px; width: 100%;">
    <h2 class="fw-bold text-muted mb-1">忘記密碼</h2>
    <p class="text-muted">請輸入註冊時使用的電子信箱，我們會寄送重設密碼的連結給你。</p>
    <hr>

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

    <form method="POST" action="{{ route('password.email') }}" class="mt-4">
      @csrf

      <div class="mb-4">
        <label for="email" class="form-label">電子信箱</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="請輸入 Email" required autofocus>
      </div>

      <button type="submit" class="btn btn-dark w-100 mb-4">寄送重設密碼連結</button>

      <div class="text-center">
        想起密碼了？<a class="ms-2 text-dark" href="{{ route('login') }}">回到登入</a>
      </div>
    </form>
  </div>
</div>
@endsection
