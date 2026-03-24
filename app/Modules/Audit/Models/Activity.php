<?php

namespace App\Modules\Audit\Models;

use App\Modules\Identity\Models\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    /**
     * Relationship to the Company (Tenant).
     * This ensures the activity log is scoped to the right multi-tenant environment.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
