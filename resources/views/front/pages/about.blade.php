
@extends('front.layouts.app')

@section('title', '關於門訓歷程')

@section('content')
<div class="container page-content pb-5" style="max-width: 800px;">
  <h2 class="fw-bold text-muted mb-4">關於門訓歷程</h2>
  <hr>

  <p class="fw-bold text-muted pt-2 pb-3" style="font-size: 1.3rem; line-height: 1.8;">
    我們相信，生命的成長不是靠一個人單打獨鬥，<br>
    而是在一段真實的關係中，被陪伴、被挑戰、被愛。
  </p>

  <p class="text-muted" style="line-height: 1.8;">
    「門訓歷程」是一個用來記錄門訓關係的平台。每一段 mentor 與 disciple 之間的同行，
    都值得被好好記下——不只是完成了幾次談話，而是這段關係走過了什麼：
    一次次的相遇、面對困難的勇氣、生命的成長、重要的決定，以及持續同行的堅持。
  </p>

  <div class="row mt-5 g-4">
    @foreach ([
        ['title' => '真實同行', 'desc' => '不是課程進度，而是真實的生命陪伴。'],
        ['title' => '完整紀錄', 'desc' => '每一次門訓、每一則留言、每一個決定都被記住。'],
        ['title' => '尊重隱私', 'desc' => '私人筆記只屬於你自己，任何人都無法查看。'],
    ] as $item)
      <div class="col-md-4">
        <div class="p-3 border rounded h-100">
          <h5 class="fw-bold">{{ $item['title'] }}</h5>
          <p class="text-muted mb-0">{{ $item['desc'] }}</p>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
