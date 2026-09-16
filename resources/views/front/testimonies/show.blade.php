
@extends('front.layouts.app')

@section('title', $testimony->title)

@section('content')
<div class="page-content pb-5">
  <div class="container" style="max-width: 720px;">

    @if (! $testimony->is_active)
      <div class="alert alert-warning">這篇見證目前是「未啟用」狀態，只有管理員看得到這個預覽。</div>
    @endif

    <a href="{{ route('home') }}#testimonies" class="text-muted small text-decoration-none">← 回到首頁</a>

    <h1 class="fw-bold text-muted mt-3 mb-4" style="font-size: 1.8rem;">{{ $testimony->title }}</h1>

    @if ($testimony->image_path)
      <div class="text-center mb-4">
        <img src="{{ asset($testimony->image_path) }}" alt="{{ $testimony->title }}" class="rounded shadow-sm" style="width: 80%; max-height: 380px; object-fit: cover;">
      </div>
    @endif

    <hr class="mx-3 navHr">
    
    <div style="line-height: 1.9;">
      @foreach (preg_split('/\n\s*\n/', trim($testimony->content)) as $paragraph)
        <p>{{ $paragraph }}</p>
      @endforeach
    </div>
  </div>
</div>
@endsection
