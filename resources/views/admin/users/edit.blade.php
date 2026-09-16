
@extends('admin.layouts.app')

@section('title', '編輯使用者')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯使用者</h2>

<form method="POST" action="{{ route('admin.users.update', $user) }}" style="max-width: 500px;">
  @csrf
  @method('PUT')
  @include('admin.users._form')
</form>
@endsection
