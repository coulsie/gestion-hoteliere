<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CateringItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class CateringItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CateringItem');
    }

    public function view(AuthUser $authUser, CateringItem $cateringItem): bool
    {
        return $authUser->can('View:CateringItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CateringItem');
    }

    public function update(AuthUser $authUser, CateringItem $cateringItem): bool
    {
        return $authUser->can('Update:CateringItem');
    }

    public function delete(AuthUser $authUser, CateringItem $cateringItem): bool
    {
        return $authUser->can('Delete:CateringItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CateringItem');
    }

    public function restore(AuthUser $authUser, CateringItem $cateringItem): bool
    {
        return $authUser->can('Restore:CateringItem');
    }

    public function forceDelete(AuthUser $authUser, CateringItem $cateringItem): bool
    {
        return $authUser->can('ForceDelete:CateringItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CateringItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CateringItem');
    }

    public function replicate(AuthUser $authUser, CateringItem $cateringItem): bool
    {
        return $authUser->can('Replicate:CateringItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CateringItem');
    }

}