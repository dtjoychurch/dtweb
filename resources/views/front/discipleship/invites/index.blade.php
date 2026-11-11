
@extends('front.layouts.app')

@section('title', '申請門訓關係')

@section('content')
<div class="container page-content pb-5" style="max-width: 700px;">
  <a href="{{ route('discipleship.index') }}" class="text-muted small text-decoration-none">← 回到我的門訓</a>

  <h2 class="fw-bold text-muted mt-2 mb-1">申請門訓關係</h2>
  <p class="text-muted small">同時最多只能有 {{ $maxPending }} 筆申請在等待處理（包括還沒被兌換的邀請碼、已兌換但還在等管理員審核的申請）。你目前有 {{ $pendingCount }} 筆。</p>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- ============ 產生邀請碼 =========== -->
  <h5 class="fw-bold text-muted border-bottom pb-2 mb-3 mt-4">產生邀請碼</h5>
  <p class="text-muted small">選擇你想在這段門訓關係裡擔任的角色，產生邀請碼後分享給對方，讓對方輸入這個碼來建立關係。送出的申請會先等管理員審核。</p>

  <form method="POST" action="{{ route('discipleship.invites.store') }}" class="border rounded p-3 mb-4">
    @csrf
    <div class="mb-2">
      <label class="form-label">我的角色</label>
      <select name="role" class="form-select" style="max-width: 300px;">
        <option value="mentor">我是 Mentor（我要帶領對方）</option>
        <option value="disciple">我是 Disciple（對方要帶領我）</option>
      </select>
    </div>
    <button type="submit" class="btn btn-dark btn-sm">產生邀請碼</button>
  </form>
  <!-- ============ 產生邀請碼 =========== -->

  <!-- ============ 輸入邀請碼 =========== -->
  <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">輸入邀請碼</h5>
  <p class="text-muted small">收到對方的邀請碼後，在這裡輸入就能建立門訓關係申請。</p>

  <form method="POST" action="{{ route('discipleship.invites.redeem') }}" class="border rounded p-3 mb-4">
    @csrf
    <div class="input-group" style="max-width: 300px;">
      <input type="text" name="code" class="form-control text-uppercase" placeholder="輸入邀請碼" maxlength="8" required>
      <button type="submit" class="btn btn-dark">送出</button>
    </div>
  </form>
  <!-- ============ 輸入邀請碼 =========== -->

  <!-- ============ 我產生的邀請碼 =========== -->
  <h5 class="fw-bold text-muted border-bottom pb-2 mb-3">我產生的邀請碼</h5>

  @if ($myInvites->isEmpty())
    <p class="text-muted">還沒有產生過邀請碼。</p>
  @else
    <div class="list-group">
      @foreach ($myInvites as $invite)
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <code class="fs-5">{{ $invite->code }}</code>
            <span class="text-muted small ms-2">
              （{{ $invite->role === 'mentor' ? '我是 Mentor' : '我是 Disciple' }}）
            </span>
            <div class="text-muted small">
              @if ($invite->status === 'pending')
                等待對方兌換中，{{ $invite->expires_at->format('Y/m/d H:i') }} 前有效
              @elseif ($invite->status === 'redeemed')
                已被 {{ $invite->redeemer?->name }} 兌換，等待管理員審核
              @elseif ($invite->status === 'expired')
                已過期
              @else
                已取消
              @endif
            </div>
          </div>
          @if ($invite->status === 'pending')
            <form method="POST" action="{{ route('discipleship.invites.destroy', $invite) }}" onsubmit="return confirm('確定要取消這個邀請碼嗎？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-link text-danger p-0">取消</button>
            </form>
          @endif
        </div>
      @endforeach
    </div>
  @endif
  <!-- ============ 我產生的邀請碼 =========== -->
</div>
@endsection
