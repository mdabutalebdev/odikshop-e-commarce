<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = ProductReview::with('product')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'all' => ProductReview::count(),
            'pending' => ProductReview::where('status', 'pending')->count(),
            'approved' => ProductReview::where('status', 'approved')->count(),
            'rejected' => ProductReview::where('status', 'rejected')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'counts'));
    }

    public function updateStatus(Request $request, ProductReview $review)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $review->update($data);

        return back()->with('success', 'Review marked as '.$data['status'].'.');
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
