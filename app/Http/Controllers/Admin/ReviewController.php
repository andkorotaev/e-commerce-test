<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviews) {}

    public function index(): View
    {
        return view('admin.reviews.index', [
            'reviews' => $this->reviews->all(),
        ]);
    }

    public function approve(int $reviewId): RedirectResponse
    {
        $this->reviews->approve($reviewId);

        return redirect()->route('admin.reviews.index');
    }

    public function destroy(int $reviewId): RedirectResponse
    {
        $this->reviews->delete($reviewId);

        return redirect()->route('admin.reviews.index');
    }
}
