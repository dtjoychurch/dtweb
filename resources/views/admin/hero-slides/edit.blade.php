
@extends('admin.layouts.app')

@section('title', '編輯 Hero 圖片')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯 Hero 圖片</h2>

<form method="POST" action="{{ route('admin.hero-slides.update', $heroSlide) }}" enctype="multipart/form-data" style="max-width: 600px;">
  @csrf
  @method('PUT')
  @include('admin.hero-slides._form')
</form>
@endsection
