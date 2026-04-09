<?php

namespace App\Modules\Websites\Policies;

use App\Modules\Identity\Models\User;
use App\Modules\Websites\Models\SiteBanner;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiteBannerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtering handled by Table Query
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SiteBanner $siteBanner): bool
    {
        return $user->company_id === $siteBanner->site->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Handled by request validation and ListBanners mount
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SiteBanner $siteBanner): bool
    {
        return $user->company_id === $siteBanner->site->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SiteBanner $siteBanner): bool
    {
        return $user->company_id === $siteBanner->site->company_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SiteBanner $siteBanner): bool
    {
        return $user->company_id === $siteBanner->site->company_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SiteBanner $siteBanner): bool
    {
        return $user->company_id === $siteBanner->site->company_id;
    }
}
