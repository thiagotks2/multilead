<?php

namespace App\Modules\Websites\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SiteBanner extends Model
{
    /** @use HasFactory<\Database\Factories\SiteBannerFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function newFactory()
    {
        return \Database\Factories\SiteBannerFactory::new();
    }

    protected $fillable = [
        'site_banner_place_id',
        'title',
        'description',
        'image_path',
        'link_url',
        'action_label',
        'display_until',
    ];

    protected function casts(): array
    {
        return [
            'display_until' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('site_banner');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (isset($activity->subject->place->site->company_id)) {
            $activity->company_id = $activity->subject->place->site->company_id;
        }
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(SiteBannerPlace::class, 'site_banner_place_id');
    }
}
