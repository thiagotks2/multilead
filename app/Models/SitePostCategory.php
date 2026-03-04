<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SitePostCategory extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'description',
        'seo_settings',
        'script_settings',
    ];

    protected function casts(): array
    {
        return [
            'seo_settings' => 'array',
            'script_settings' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('site_post_category');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if ($activity->subject->site_id && isset($activity->subject->site->company_id)) {
            $activity->company_id = $activity->subject->site->company_id;
        }
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function posts(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(SitePost::class, 'categorizable', 'site_post_categorizables');
    }
}
