
@extends('admin.layouts.app')

@section('title', '新增門訓關係')

@section('content')
<h2 class="fw-bold text-muted mb-4">新增門訓關係</h2>

<form method="POST" action="{{ route('admin.relationships.store') }}" style="max-width: 600px;">
  @csrf
  @include('admin.relationships._form')
</form>
@endsection
