
@extends('admin.layouts.app')

@section('title', '新增見證分享')

@section('content')
<h2 class="fw-bold text-muted mb-4">新增見證分享</h2>

<form method="POST" action="{{ route('admin.testimonies.store') }}" enctype="multipart/form-data" style="max-width: 600px;">
  @csrf
  @include('admin.testimonies._form')
</form>
@endsection
