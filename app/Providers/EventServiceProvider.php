<?php

namespace App\Providers;

use App\Events\Auth\PasswordReset;
use App\Events\Auth\UserLoggedIn;
use App\Events\Auth\UserRegistered;
use App\Events\Backend\BookingModified;
use App\Events\Backend\ClientCreated;
use App\Events\Backend\ContactMessageReplied;
use App\Events\Backend\PageModified;
use App\Events\Backend\RoleModified;
use App\Events\Backend\ServiceModified;
use App\Events\Backend\SettingsUpdated;
use App\Events\Backend\StaffPermissionsUpdated;
use App\Listeners\Auth\LogPasswordReset;
use App\Listeners\Auth\LogSuccessfulLogin;
use App\Listeners\Auth\SendEmailVerificationNotification;
use App\Listeners\Backend\LogBookingActivity;
use App\Listeners\Backend\LogCmsAndSettingsActivity;
use App\Listeners\Backend\LogPermissionChange;
use App\Listeners\Backend\LogRoleActivity;
use App\Listeners\Backend\LogServiceActivity;
use App\Listeners\Backend\SendClientWelcomeNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserLoggedIn::class => [
            LogSuccessfulLogin::class,
        ],
        PasswordReset::class => [
            LogPasswordReset::class,
        ],
        StaffPermissionsUpdated::class => [
            LogPermissionChange::class,
        ],
        RoleModified::class => [
            LogRoleActivity::class,
        ],

        ServiceModified::class => [
            LogServiceActivity::class,
        ],

        BookingModified::class => [
            LogBookingActivity::class,
        ],
        ClientCreated::class => [
            SendClientWelcomeNotification::class,
        ],
        SettingsUpdated::class => [
            LogCmsAndSettingsActivity::class,
        ],
        PageModified::class => [
            LogCmsAndSettingsActivity::class,
        ],
        ContactMessageReplied::class => [
            LogCmsAndSettingsActivity::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
