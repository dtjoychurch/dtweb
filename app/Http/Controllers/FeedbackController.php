<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
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
}
