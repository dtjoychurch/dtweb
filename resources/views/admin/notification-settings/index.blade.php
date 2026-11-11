
@extends('admin.layouts.app')

@section('title', '通知設定')

@section('content')
<h2 class="fw-bold text-muted mb-3">通知設定</h2>
<p class="text-muted small">管理系統會在什麼情況下通知使用者，以及通知的文字內容。文字內容裡的 <code>:user</code>、<code>:ordinal</code> 會被自動替換成實際的人名和次數。</p>

@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

@foreach ($settings as $setting)
  <div class="card bg-white shadow-sm mb-3" style="max-width: 700px;">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.notification-settings.update', $setting) }}">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">{{ $setting->label }}</h5>
          <div class="form-check form-switch">
            <input type="hidden" name="is_enabled" value="0">
            <input type="checkbox" name="is_enabled" value="1" class="form-check-input" id="enabled-{{ $setting->id }}" {{ $setting->is_enabled ? 'checked' : '' }}>
            <label class="form-check-label" for="enabled-{{ $setting->id }}">啟用</label>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">通知文字內容</label>
          <textarea name="message_template" class="form-control" rows="2">{{ old('message_template', $setting->message_template) }}</textarea>
        </div>

        <button type="submit" class="btn btn-dark btn-sm">儲存</button>
      </form>
    </div>
  </div>
@endforeach
@endsection
