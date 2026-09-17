
@extends('admin.layouts.app')

@section('title', '新增紀錄類型')

@section('content')
<h2 class="fw-bold text-muted mb-4">新增紀錄類型</h2>

<form method="POST" action="{{ route('admin.record-types.store') }}" style="max-width: 500px;">
  @csrf
  @include('admin.record-types._form')
</form>
@endsection
