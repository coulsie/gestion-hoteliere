<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EventBooking;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventBookingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventBooking');
    }

    public function view(AuthUser $authUser, EventBooking $eventBooking): bool
    {
        return $authUser->can('View:EventBooking');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventBooking');
    }

    public function update(AuthUser $authUser, EventBooking $eventBooking): bool
    {
        return $authUser->can('Update:EventBooking');
    }

    public function delete(AuthUser $authUser, EventBooking $eventBooking): bool
    {
        return $authUser->can('Delete:EventBooking');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventBooking');
    }

    public function restore(AuthUser $authUser, EventBooking $eventBooking): bool
    {
        return $authUser->can('Restore:EventBooking');
    }

    public function forceDelete(AuthUser $authUser, EventBooking $eventBooking): bool
    {
        return $authUser->can('ForceDelete:EventBooking');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventBooking');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventBooking');
    }

    public function replicate(AuthUser $authUser, EventBooking $eventBooking): bool
    {
        return $authUser->can('Replicate:EventBooking');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventBooking');
    }

}