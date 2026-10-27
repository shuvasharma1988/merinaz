<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $page = Page::query()
            ->where('slug', 'about')
            ->where('is_published', true)
            ->first();

        return view('frontend.about', compact('page'));
    }

    public function privacy(): View
    {
        $page = Page::query()
            ->whereIn('slug', ['privacy-policy', 'privacy'])
            ->where('is_published', true)
            ->first();

        return view('frontend.privacy', compact('page'));
    }
}
