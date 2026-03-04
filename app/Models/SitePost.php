<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SitePost extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'site_id',
        'title',
        'slug',
        'content',
        'featured_image_path',
        'seo_settings',
        'script_settings',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'seo_settings' => 'array',
            'script_settings' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('site_post');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (isset($activity->subject->site->company_id)) {
            $activity->company_id = $activity->subject->site->company_id;
        }
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(SitePostCategory::class, 'categorizable', 'site_post_categorizables');
    }
}
