<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CateringOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class CateringOrderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CateringOrder');
    }

    public function view(AuthUser $authUser, CateringOrder $cateringOrder): bool
    {
        return $authUser->can('View:CateringOrder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CateringOrder');
    }

    public function update(AuthUser $authUser, CateringOrder $cateringOrder): bool
    {
        return $authUser->can('Update:CateringOrder');
    }

    public function delete(AuthUser $authUser, CateringOrder $cateringOrder): bool
    {
        return $authUser->can('Delete:CateringOrder');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CateringOrder');
    }

    public function restore(AuthUser $authUser, CateringOrder $cateringOrder): bool
    {
        return $authUser->can('Restore:CateringOrder');
    }

    public function forceDelete(AuthUser $authUser, CateringOrder $cateringOrder): bool
    {
        return $authUser->can('ForceDelete:CateringOrder');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CateringOrder');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CateringOrder');
    }

    public function replicate(AuthUser $authUser, CateringOrder $cateringOrder): bool
    {
        return $authUser->can('Replicate:CateringOrder');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CateringOrder');
    }

}