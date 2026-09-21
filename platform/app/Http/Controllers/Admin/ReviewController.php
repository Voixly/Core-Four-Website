<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->with(['assignee', 'lead'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->q, function ($q, $term) {
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', '%'.$term.'%')
                        ->orWhere('city', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%')
                        ->orWhere('job', 'like', '%'.$term.'%');
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'held' => Review::query()->where('status', 'held')->count(),
            'invited' => Review::query()->where('status', 'invited')->count(),
            'pending' => Review::query()->where('status', 'pending')->count(),
            'googleClicks' => Review::query()->whereNotNull('google_clicked_at')->count(),
        ]);
    }

    public function show(Review $review): View
    {
        $review->load(['lead', 'assignee']);
        $staff = User::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.reviews.show', compact('review', 'staff'));
    }

    public function store(Request $request, ReviewService $reviews): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', 'in:residential,commercial'],
            'job' => ['nullable', 'string', 'max:120'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'job_id' => ['nullable', 'exists:roofing_jobs,id'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['lead_id'])) {
            $review = $reviews->inviteFromLead(
                Lead::query()->findOrFail($data['lead_id']),
                $request->boolean('send_email')
            );
            if (! empty($data['job_id']) && ! $review->job_id) {
                $review->update(['job_id' => $data['job_id']]);
            }
        } else {
            $review = $reviews->invite($data, $request->user(), $request->boolean('send_email'));
        }

        return redirect()
            ->route('admin.reviews.show', $review)
            ->with('success', 'Review Shield link ready.');
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Review::STATUSES)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'recovery_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $review->update($data);

        return back()->with('success', 'Review updated.');
    }
}
