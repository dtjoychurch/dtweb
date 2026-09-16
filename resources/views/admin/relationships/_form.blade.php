@php $relationship = $relationship ?? null; @endphp

<div class="row">
  <div class="col-md-6 mb-3">
    <label class="form-label">Mentor</label>
    <input type="text" name="mentor_search" id="mentor_search" class="form-control" list="userOptions" autocomplete="off" placeholder="輸入姓名搜尋"
      value="{{ old('mentor_search', $relationship?->mentor ? $relationship->mentor->name.'（'.$relationship->mentor->email.'）' : '') }}">
    <input type="hidden" name="mentor_id" id="mentor_id" value="{{ old('mentor_id', $relationship->mentor_id ?? '') }}">
  </div>
  <div class="col-md-6 mb-3">
    <label class="form-label">Disciple</label>
    <input type="text" name="disciple_search" id="disciple_search" class="form-control" list="userOptions" autocomplete="off" placeholder="輸入姓名搜尋"
      value="{{ old('disciple_search', $relationship?->disciple ? $relationship->disciple->name.'（'.$relationship->disciple->email.'）' : '') }}">
    <input type="hidden" name="disciple_id" id="disciple_id" value="{{ old('disciple_id', $relationship->disciple_id ?? '') }}">
  </div>

  <datalist id="userOptions">
    @foreach ($users as $u)
      <option value="{{ $u->name }}（{{ $u->email }}）"></option>
    @endforeach
  </datalist>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">開始日期</label>
    <input type="date" name="started_at" class="form-control" value="{{ old('started_at', isset($relationship) ? $relationship->started_at->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">結束日期（選填）</label>
    <input type="date" name="ended_at" class="form-control" value="{{ old('ended_at', $relationship?->ended_at?->format('Y-m-d') ?? '') }}">
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">狀態</label>
    <select name="status" class="form-select">
      <option value="active" {{ old('status', $relationship->status ?? 'active') === 'active' ? 'selected' : '' }}>進行中</option>
      <option value="paused" {{ old('status', $relationship->status ?? '') === 'paused' ? 'selected' : '' }}>暫停中</option>
      <option value="completed" {{ old('status', $relationship->status ?? '') === 'completed' ? 'selected' : '' }}>已完成</option>
    </select>
  </div>
</div>

<button type="submit" class="btn btn-dark">儲存</button>
<a href="{{ route('admin.relationships.index') }}" class="btn btn-link text-muted">取消</a>

<script>
  (function () {
    // datalist 只負責「顯示建議、讓使用者用打字搜尋」，選到的文字要對回真正的 user id，
    // 才能塞進真正送出的 hidden input。
    const userMap = {
      @foreach ($users as $u)
        {{ \Illuminate\Support\Js::from($u->name.'（'.$u->email.'）') }}: {{ $u->id }},
      @endforeach
    };

    function bind(searchId, hiddenId) {
      const search = document.getElementById(searchId);
      const hidden = document.getElementById(hiddenId);
      if (!search || !hidden) return;

      search.addEventListener('input', function () {
        const id = userMap[search.value];
        hidden.value = id ?? '';
      });
    }

    bind('mentor_search', 'mentor_id');
    bind('disciple_search', 'disciple_id');
  })();
</script>
