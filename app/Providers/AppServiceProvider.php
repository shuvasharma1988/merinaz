<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\StaffPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, StaffPolicy::class);
        Gate::policy(Appointment::class, BookingPolicy::class);

        // Define the global password security rules
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase() // Requires at least one uppercase and one lowercase letter
                ->numbers()   // Requires at least one number
                ->symbols();  // Requires at least one special character (e.g., !@#$%^&*)
        });
    }
}
