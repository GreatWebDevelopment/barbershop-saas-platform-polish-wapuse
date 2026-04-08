<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $shopId = $request->user()->shop_id;

        $query = Review::where('shop_id', $shopId)
            ->with('customer')
            ->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $reviews = $query->paginate(20)->withQueryString();

        $stats = [
            'average' => Review::where('shop_id', $shopId)->avg('rating'),
            'total' => Review::where('shop_id', $shopId)->count(),
            'distribution' => Review::where('shop_id', $shopId)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray(),
        ];

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => $request->only(['rating', 'source']),
        ]);
    }
}
