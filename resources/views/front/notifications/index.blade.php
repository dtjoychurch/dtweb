
@extends('front.layouts.app')

@section('title', '通知')

@section('content')
<div class="container page-content pb-5">
  <h2 class="fw-bold text-muted mb-4">通知</h2>

  @if ($notifications->isEmpty())
    <p class="text-muted mt-4">還沒有任何通知。</p>
  @else
    <div class="list-group">
      @foreach ($notifications as $notification)
        <a
          href="{{ $notification->url ?? '#' }}"
          class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-light' }}"
        >
          <div class="d-flex justify-content-between align-items-start">
            <p class="mb-1">{{ $notification->message }}</p>
            @unless ($notification->read_at)
              <span class="badge bg-dark ms-2">新</span>
            @endunless
          </div>
          <div class="text-muted small">{{ $notification->created_at->format('Y/m/d H:i') }}</div>
        </a>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $notifications->links() }}
    </div>
  @endif
</div>
@endsection
