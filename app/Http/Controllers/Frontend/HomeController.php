<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Review;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(6)
            ->get();

        $posts = Post::query()
            ->published()
            ->with(['category', 'author'])
            ->latestFirst()
            ->take(3)
            ->get();

        $reviews = Review::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('service')
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('frontend.home', compact('services', 'posts', 'reviews'));
    }
}
