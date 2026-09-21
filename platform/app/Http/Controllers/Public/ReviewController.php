<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function show(?string $token = null): View
    {
        $review = $token
            ? Review::query()->where('token', $token)->firstOrFail()
            : null;

        if ($review?->stars) {
            return view('public.reviews.result', [
                'review' => $review,
                'googleUrl' => $review->isHappy() ? route('reviews.google', $review->token) : null,
                'yelpUrl' => $review->isHappy() ? route('reviews.yelp', $review->token) : null,
            ]);
        }

        return view('public.reviews.form', compact('review'));
    }

    public function store(Request $request, ReviewService $reviews, ?string $token = null): RedirectResponse
    {
        $data = $request->validate([
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', 'in:residential,commercial'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = $token
            ? Review::query()->where('token', $token)->firstOrFail()
            : $reviews->invite([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'] ?? null,
                'type' => $data['type'] ?? 'residential',
                'source' => 'public',
            ]);

        if ($review->stars) {
            return redirect()->away('/reviews/'.$review->token.'/');
        }

        $reviews->rate($review, $data);

        return redirect()->away('/reviews/'.$review->token.'/');
    }

    public function google(ReviewService $reviews, string $token): RedirectResponse
    {
        $review = Review::query()->where('token', $token)->firstOrFail();
        abort_unless($review->isHappy(), 404);
        $review->forceFill(['google_clicked_at' => $review->google_clicked_at ?? now()])->save();

        return redirect()->away($reviews->googleUrl());
    }

    public function yelp(ReviewService $reviews, string $token): RedirectResponse
    {
        $review = Review::query()->where('token', $token)->firstOrFail();
        abort_unless($review->isHappy(), 404);
        $review->forceFill(['yelp_clicked_at' => $review->yelp_clicked_at ?? now()])->save();

        return redirect()->away($reviews->yelpUrl());
    }
}
