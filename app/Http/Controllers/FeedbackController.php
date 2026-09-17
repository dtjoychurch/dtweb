<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Auth::user()->feedbacks()->latest()->paginate(10);

        return view('front.feedback.index', compact('feedbacks'));
    }

    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        Auth::user()->feedbacks()->create($request->validated());

        return redirect()->route('feedback.index')->with('status', '感謝你的意見，我們收到了！');
    }

    public function destroy(Feedback $feedback): RedirectResponse
    {
        $this->authorize('delete', $feedback);

        $feedback->delete();

        return redirect()->route('feedback.index')->with('status', '已刪除這則意見。');
    }
}
