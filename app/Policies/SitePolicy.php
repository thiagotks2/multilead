<?php

namespace App\Policies;

use App\Models\Site;
use Illuminate\Contracts\Auth\Authenticatable;

class SitePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authenticatable $user, Site $site): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authenticatable $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authenticatable $user, Site $site): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authenticatable $user, Site $site): bool
    {
        $panel = filament()->getCurrentPanel();

        return ! ($panel && $panel->getId() === 'app');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authenticatable $user, Site $site): bool
    {
        $panel = filament()->getCurrentPanel();

        return ! ($panel && $panel->getId() === 'app');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authenticatable $user, Site $site): bool
    {
        $panel = filament()->getCurrentPanel();

        return ! ($panel && $panel->getId() === 'app');
    }
}
