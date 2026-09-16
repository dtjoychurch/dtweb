
@extends('admin.layouts.app')

@section('title', '新增使用者')

@section('content')
<h2 class="fw-bold text-muted mb-4">新增使用者</h2>

<form method="POST" action="{{ route('admin.users.store') }}" style="max-width: 500px;">
  @csrf
  @include('admin.users._form')
</form>
@endsection
