<?php

namespace App\Http\Controllers\Backend;

use App\Events\Backend\SettingsUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpdateSiteSettingsRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        Gate::authorize('viewAny', SiteSetting::class);

        $rawSettings = SiteSetting::pluck('value', 'key')->toArray();

        // Decode JSON business_hours so $settings['business_hours']['monday_friday'] works in Blade
        if (isset($rawSettings['business_hours']) && is_string($rawSettings['business_hours'])) {
            $decoded = json_decode($rawSettings['business_hours'], true);
            $rawSettings['business_hours'] = is_array($decoded) ? $decoded : [];
        }

        $settings = $rawSettings;

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSiteSettingsRequest $request): RedirectResponse
    {
        Gate::authorize('update', SiteSetting::class);

        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'type' => is_array($value) ? 'json' : 'text',
                ]
            );
        }

        Cache::forget('site_settings_all');

        event(new SettingsUpdated($request->user(), array_keys($validated)));

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Site settings updated successfully.');
    }
}
