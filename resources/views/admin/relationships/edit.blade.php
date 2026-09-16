
@extends('admin.layouts.app')

@section('title', '編輯門訓關係')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯門訓關係</h2>

<form method="POST" action="{{ route('admin.relationships.update', $relationship) }}" style="max-width: 600px;">
  @csrf
  @method('PUT')
  @include('admin.relationships._form')
</form>
@endsection
