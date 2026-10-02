<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Review;
use App\Models\ReviewMailLog;
use App\Support\ReviewMailSchema;
use App\Models\User;
use App\Services\JobNimbusService;
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

        ReviewMailSchema::ensure();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'held' => Review::query()->where('status', 'held')->count(),
            'invited' => Review::query()->where('status', 'invited')->count(),
            'pending' => Review::query()->where('status', 'pending')->count(),
            'googleClicks' => Review::query()->whereNotNull('google_clicked_at')->count(),
            'mailsSent' => ReviewMailLog::query()->where('status', 'sent')->count(),
            'mailsFailed' => ReviewMailLog::query()->where('status', 'failed')->count(),
            'mailLog' => ReviewMailLog::query()->with('review')->orderByRaw('coalesce(sent_at, scheduled_at, created_at) desc')->limit(15)->get(),
            'pendingUnsent' => Review::query()
                ->where('status', 'pending')
                ->whereNull('stars')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->whereDoesntHave('mailLogs', function ($query) {
                    $query->whereIn('status', ['sent', 'scheduled']);
                })
                ->count(),
        ]);
    }

    public function show(Review $review): View
    {
        $review->load(['lead', 'assignee', 'mailLogs']);
        $staff = User::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.reviews.show', compact('review', 'staff'));
    }

    public function sendPending(ReviewService $reviews): RedirectResponse
    {
        $result = $reviews->sendPending();
        $parts = [];

        if ($result['sent'] > 0) {
            $parts[] = 'Sent the first note to '.$result['sent'].' '.($result['sent'] === 1 ? 'customer' : 'customers').'. Three more follow unless they rate.';
        }
        if ($result['failed'] > 0) {
            $parts[] = $result['failed'].' did not send.';
        }
        if ($result['missing_email'] > 0) {
            $parts[] = $result['missing_email'].' '.($result['missing_email'] === 1 ? 'has' : 'have').' no email address.';
        }
        if ($parts === []) {
            $parts[] = 'Every waiting customer has already been emailed.';
        }

        return back()->with($result['sent'] === 0 && $result['failed'] > 0 ? 'error' : 'success', implode(' ', $parts));
    }

    public function syncJobNimbus(JobNimbusService $jobNimbus): RedirectResponse
    {
        $result = $jobNimbus->pullCompletedReviews();

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
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

        $message = match ($review->emailResult ?? null) {
            'sent' => 'Review Shield link ready. The first note was sent. Three more follow unless they rate.',
            'rated' => 'They already rated this job, so no more emails went out.',
            'failed' => 'Review Shield link ready. The rating email did not send.',
            'skipped' => 'Review Shield link ready. Add an email address to send the rating note.',
            default => 'Review Shield link ready.',
        };

        return redirect()
            ->route('admin.reviews.show', $review)
            ->with(($review->emailResult ?? null) === 'failed' ? 'error' : 'success', $message);
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
