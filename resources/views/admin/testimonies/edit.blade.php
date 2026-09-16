
@extends('admin.layouts.app')

@section('title', '編輯見證分享')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯見證分享</h2>

<form method="POST" action="{{ route('admin.testimonies.update', $testimony) }}" enctype="multipart/form-data" style="max-width: 600px;">
  @csrf
  @method('PUT')
  @include('admin.testimonies._form')
</form>
@endsection
