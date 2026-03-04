<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SiteBannerPlace extends Model
{
    /** @use HasFactory<\Database\Factories\SiteBannerPlaceFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'site_id',
        'name',
        'description',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('site_banner_place');
    }

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        if (isset($activity->subject->site->company_id)) {
            $activity->company_id = $activity->subject->site->company_id;
        }
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function banners(): HasMany
    {
        return $this->hasMany(SiteBanner::class);
    }
}
