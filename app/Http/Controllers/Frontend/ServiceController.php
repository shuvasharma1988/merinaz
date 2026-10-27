<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->where('is_active', true)
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('frontend.services.index', compact('services', 'categories'));
    }

    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404);

        $service->load(['category', 'faqs' => fn ($query) => $query->published()->ordered()]);

        $related = Service::query()
            ->where('is_active', true)
            ->where('id', '!=', $service->id)
            ->when(
                $service->category_id !== null,
                fn ($query) => $query->where('category_id', $service->category_id),
            )
            ->orderBy('sort_order')
            ->take(2)
            ->get();

        if ($related->count() < 2) {
            $related = $related->concat(
                Service::query()
                    ->where('is_active', true)
                    ->where('id', '!=', $service->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->orderBy('sort_order')
                    ->take(2 - $related->count())
                    ->get()
            );
        }

        return view('frontend.services.show', compact('service', 'related'));
    }
}
