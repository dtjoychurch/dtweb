
@extends('admin.layouts.app')

@section('title', '新增 Hero 圖片')

@section('content')
<h2 class="fw-bold text-muted mb-4">新增 Hero 圖片</h2>

<form method="POST" action="{{ route('admin.hero-slides.store') }}" enctype="multipart/form-data" style="max-width: 600px;">
  @csrf
  @include('admin.hero-slides._form')
</form>
@endsection
