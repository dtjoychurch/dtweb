<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Feedback::class, 'feedback', ['except' => ['create', 'store', 'edit', 'update']]);
    }

    public function index(Request $request)
    {
        $feedbacks = Feedback::with('user')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('user');

        return view('admin.feedbacks.show', compact('feedback'));
    }

    public function destroy(Feedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return redirect()->route('admin.feedbacks.index')->with('status', '已刪除意見。');
    }
}
