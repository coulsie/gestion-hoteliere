<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EventSpace;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventSpacePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EventSpace');
    }

    public function view(AuthUser $authUser, EventSpace $eventSpace): bool
    {
        return $authUser->can('View:EventSpace');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EventSpace');
    }

    public function update(AuthUser $authUser, EventSpace $eventSpace): bool
    {
        return $authUser->can('Update:EventSpace');
    }

    public function delete(AuthUser $authUser, EventSpace $eventSpace): bool
    {
        return $authUser->can('Delete:EventSpace');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:EventSpace');
    }

    public function restore(AuthUser $authUser, EventSpace $eventSpace): bool
    {
        return $authUser->can('Restore:EventSpace');
    }

    public function forceDelete(AuthUser $authUser, EventSpace $eventSpace): bool
    {
        return $authUser->can('ForceDelete:EventSpace');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EventSpace');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EventSpace');
    }

    public function replicate(AuthUser $authUser, EventSpace $eventSpace): bool
    {
        return $authUser->can('Replicate:EventSpace');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EventSpace');
    }

}