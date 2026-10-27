<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $categories = BlogCategory::query()
            ->active()
            ->ordered()
            ->get();

        $posts = Post::query()
            ->published()
            ->with(['category', 'author'])
            ->when($request->filled('category'), fn ($query) => $query->whereHas(
                'category',
                fn ($categoryQuery) => $categoryQuery->where('slug', $request->string('category')->toString()),
            ))
            ->when($request->filled('search'), fn ($query) => $query->where(
                'title',
                'like',
                '%'.$request->string('search')->toString().'%'
            ))
            ->latestFirst()
            ->paginate(9)
            ->withQueryString();

        return view('frontend.blog.index', compact('posts', 'categories'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPubliclyVisible(), 404);

        $post->load(['category', 'author']);

        $related = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when(
                $post->category_id !== null,
                fn ($query) => $query->where('category_id', $post->category_id),
            )
            ->latestFirst()
            ->take(2)
            ->get();

        if ($related->count() < 2) {
            $related = $related->concat(
                Post::query()
                    ->published()
                    ->where('id', '!=', $post->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->latestFirst()
                    ->take(2 - $related->count())
                    ->get()
            );
        }

        return view('frontend.blog.show', compact('post', 'related'));
    }
}
