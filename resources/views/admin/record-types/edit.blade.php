
@extends('admin.layouts.app')

@section('title', '編輯紀錄類型')

@section('content')
<h2 class="fw-bold text-muted mb-4">編輯紀錄類型</h2>

<form method="POST" action="{{ route('admin.record-types.update', $recordType) }}" style="max-width: 500px;">
  @csrf
  @method('PUT')
  @include('admin.record-types._form')
</form>
@endsection
