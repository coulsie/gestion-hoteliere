<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\KeyCard;
use Illuminate\Auth\Access\HandlesAuthorization;

class KeyCardPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KeyCard');
    }

    public function view(AuthUser $authUser, KeyCard $keyCard): bool
    {
        return $authUser->can('View:KeyCard');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KeyCard');
    }

    public function update(AuthUser $authUser, KeyCard $keyCard): bool
    {
        return $authUser->can('Update:KeyCard');
    }

    public function delete(AuthUser $authUser, KeyCard $keyCard): bool
    {
        return $authUser->can('Delete:KeyCard');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:KeyCard');
    }

    public function restore(AuthUser $authUser, KeyCard $keyCard): bool
    {
        return $authUser->can('Restore:KeyCard');
    }

    public function forceDelete(AuthUser $authUser, KeyCard $keyCard): bool
    {
        return $authUser->can('ForceDelete:KeyCard');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KeyCard');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KeyCard');
    }

    public function replicate(AuthUser $authUser, KeyCard $keyCard): bool
    {
        return $authUser->can('Replicate:KeyCard');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KeyCard');
    }

}