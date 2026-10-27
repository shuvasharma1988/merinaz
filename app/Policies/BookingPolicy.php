<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Checks for either standard naming convention
        return $user->hasPermission('bookings.view')
            || $user->hasPermission('bookings.viewAny')
            || $user->hasPermission('appointments.view');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasPermission('bookings.view') || $user->hasPermission('appointments.view')) {
            return true;
        }

        // Staff viewing only their assigned appointments
        return ($user->hasPermission('bookings.view.own') || $user->hasPermission('staff.view'))
            && $user->id === $appointment->staff_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('bookings.create')
            || $user->hasPermission('appointments.create');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasPermission('bookings.update') || $user->hasPermission('bookings.edit')) {
            return true;
        }

        return $user->hasPermission('bookings.update.own') && $user->id === $appointment->staff_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasPermission('bookings.delete');
    }
}
