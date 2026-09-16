
@extends('front.layouts.app')

@section('title', '門訓關係')

@section('content')
<div class="container page-content pb-5">

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <!-- ============ 門訓關係資訊 =========== -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="fw-bold text-muted mb-3">門訓關係</h2>
      <div class="row">
        <div class="col-6 col-md-3 mb-3">
          <div class="text-muted small">Mentor</div>
          <div class="fw-semibold">{{ $relationship->mentor->name }}</div>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <div class="text-muted small">Disciple</div>
          <div class="fw-semibold">{{ $relationship->disciple->name }}</div>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <div class="text-muted small">開始日期</div>
          <div class="fw-semibold">{{ $relationship->started_at->format('Y/m/d') }}</div>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <div class="text-muted small">狀態</div>
          <div class="fw-semibold">{{ ['active' => '進行中', 'paused' => '暫停中', 'completed' => '已完成'][$relationship->status] }}</div>
        </div>
      </div>
    </div>
  </div>
  <!-- ============ 門訓關係資訊 =========== -->

  <!-- ============ 門訓目標 =========== -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-muted mb-0">門訓目標</h4>
        <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#newGoalForm">＋ 新增目標</button>
      </div>

      <div class="collapse mb-3" id="newGoalForm">
        <form method="POST" action="{{ route('discipleship.goals.store', $relationship) }}" class="border rounded p-3">
          @csrf
          <div class="mb-2">
            <label class="form-label">標題</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">描述</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
          </div>
          <div class="row">
            <div class="col-6 mb-2">
              <label class="form-label">狀態</label>
              <select name="status" class="form-select">
                <option value="pending">尚未開始</option>
                <option value="in_progress">進行中</option>
                <option value="completed">已完成</option>
                <option value="cancelled">已取消</option>
              </select>
            </div>
            <div class="col-6 mb-2">
              <label class="form-label">可見範圍</label>
              <select name="visibility" class="form-select">
                <option value="shared">雙方可見</option>
                <option value="private">僅自己可見</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-dark btn-sm">新增</button>
        </form>
      </div>

      @if ($goals->isEmpty())
        <p class="text-muted mb-0">目前還沒有設定門訓目標。</p>
      @else
        <ul class="list-group list-group-flush">
          @foreach ($goals as $goal)
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div>
                <span class="badge {{ $goal->status === 'completed' ? 'bg-success' : ($goal->status === 'cancelled' ? 'bg-secondary' : 'bg-warning text-dark') }} me-2">
                  {{ ['pending' => '尚未開始', 'in_progress' => '進行中', 'completed' => '已完成', 'cancelled' => '已取消'][$goal->status] }}
                </span>
                <strong>{{ $goal->title }}</strong>
                @if ($goal->visibility === 'private')
                  <span class="badge bg-light text-dark border ms-1">私人</span>
                @endif
                @if ($goal->description)
                  <div class="text-muted small mt-1">{{ $goal->description }}</div>
                @endif
              </div>
              @if ($goal->created_by === auth()->id())
                <form method="POST" action="{{ route('discipleship.goals.destroy', [$relationship, $goal]) }}" onsubmit="return confirm('確定要刪除這個目標嗎？');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-link text-danger">刪除</button>
                </form>
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
  <!-- ============ 門訓目標 =========== -->

  <!-- ============ 新增紀錄按鈕 =========== -->
  <div class="d-flex gap-2 mb-4 flex-wrap">
    <button class="btn btn-dark btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#newSessionForm">＋ 新增門訓紀錄</button>
    <button class="btn btn-outline-dark btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#newRecordForm">＋ 新增生命歷程紀錄</button>
  </div>

  <div class="collapse mb-4" id="newSessionForm">
    <form method="POST" action="{{ route('discipleship.sessions.store', $relationship) }}" class="border rounded p-3" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-md-4 mb-2">
          <label class="form-label">日期</label>
          <input type="date" name="session_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-8 mb-2">
          <label class="form-label">標題（選填）</label>
          <input type="text" name="title" class="form-control">
        </div>
      </div>
      <div class="mb-2">
        <label class="form-label">門訓內容</label>
        <textarea name="content" class="form-control" rows="4" required></textarea>
      </div>
      <div class="mb-2">
        <label class="form-label">照片（選填，可多選）</label>
        <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
      </div>
      <button type="submit" class="btn btn-dark btn-sm">新增</button>
    </form>
  </div>

  <div class="collapse mb-4" id="newRecordForm">
    <form method="POST" action="{{ route('discipleship.records.store', $relationship) }}" class="border rounded p-3">
      @csrf
      <div class="row">
        <div class="col-md-4 mb-2">
          <label class="form-label">類型</label>
          <select name="type" class="form-select" required>
            <option value="growth">成長</option>
            <option value="struggle">掙扎</option>
            <option value="reflection">反思</option>
            <option value="prayer">禱告</option>
            <option value="milestone">里程碑</option>
            <option value="observation">觀察</option>
            <option value="decision">決定</option>
            <option value="testimony">見證</option>
          </select>
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label">日期</label>
          <input type="date" name="occurred_at" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-4 mb-2">
          <label class="form-label">可見範圍</label>
          <select name="visibility" class="form-select">
            <option value="shared">雙方可見</option>
            <option value="private">僅自己可見</option>
          </select>
        </div>
      </div>
      <div class="mb-2">
        <label class="form-label">標題</label>
        <input type="text" name="title" class="form-control" required>
      </div>
      <div class="mb-2">
        <label class="form-label">內容</label>
        <textarea name="content" class="form-control" rows="3" required></textarea>
      </div>
      <button type="submit" class="btn btn-outline-dark btn-sm">新增</button>
    </form>
  </div>
  <!-- ============ 新增紀錄按鈕 =========== -->

  <!-- ============ 混合時間軸 =========== -->
  <h4 class="fw-bold text-muted mb-3">門訓歷程</h4>

  @if ($timeline->isEmpty())
    <p class="text-muted">這段門訓關係還沒有任何紀錄。</p>
  @else
    <div class="timeline">
      @foreach ($timeline as $item)
        <div class="border rounded p-3 mb-3 shadow-sm">
          <div class="text-muted small mb-1">{{ $item['date']->format('Y/m/d') }}</div>

          @if ($item['type'] === 'session')
            @php $session = $item['model']; @endphp
            <h5 class="mb-1">第 {{ $session->ordinal_number }} 次門訓{{ $session->title ? '｜'.$session->title : '' }}</h5>
            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($session->content), 80) }}</p>
            <a href="{{ route('discipleship.sessions.show', [$relationship, $session]) }}" class="btn btn-sm btn-outline-dark">查看 →</a>
          @else
            @php $record = $item['model']; @endphp
            <span class="badge bg-light text-dark border mb-1">
              {{ ['growth'=>'成長','struggle'=>'掙扎','reflection'=>'反思','prayer'=>'禱告','milestone'=>'里程碑','observation'=>'觀察','decision'=>'決定','testimony'=>'見證'][$record->type] }}
            </span>
            @if ($record->visibility === 'private')
              <span class="badge bg-light text-dark border mb-1">私人</span>
            @endif
            <h5 class="mb-1">{{ $record->title }}</h5>
            <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($record->content), 100) }}</p>
            @if ($record->created_by === auth()->id())
              <form method="POST" action="{{ route('discipleship.records.destroy', [$relationship, $record]) }}" onsubmit="return confirm('確定要刪除這筆紀錄嗎？');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-link text-danger p-0">刪除</button>
              </form>
            @endif
          @endif
        </div>
      @endforeach
    </div>
  @endif
  <!-- ============ 混合時間軸 =========== -->
</div>
@endsection
