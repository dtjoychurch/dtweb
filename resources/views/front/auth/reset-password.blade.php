
@extends('front.layouts.app')

@section('title', '重設密碼')

@section('content')
<div class="container page-content pb-5 d-flex justify-content-center">
  <div style="max-width: 420px; width: 100%;">
    <h2 class="fw-bold text-muted mb-1">重設密碼</h2>
    <p class="text-muted">請輸入你的新密碼。</p>
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

    <form method="POST" action="{{ route('password.update') }}" class="mt-4">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <div class="mb-3">
        <label for="email" class="form-label">電子信箱</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email) }}" placeholder="請輸入 Email" required autofocus>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">新密碼</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="請輸入新密碼" required>
      </div>

      <div class="mb-4">
        <label for="password_confirmation" class="form-label">確認新密碼</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="請再輸入一次新密碼" required>
      </div>

      <button type="submit" class="btn btn-dark w-100">重設密碼</button>
    </form>
  </div>
</div>
@endsection
