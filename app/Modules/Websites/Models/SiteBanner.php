<?php

namespace App\Modules\Websites\Models;

use App\Modules\Identity\Models\Company;
use App\Modules\Websites\Enums\BannerType;
use Database\Factories\SiteBannerFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SiteBanner extends Model
{
    /** @use HasFactory<SiteBannerFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function newFactory()
    {
        return SiteBannerFactory::new();
    }

    protected $fillable = [
        'site_id',
        'type',
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
            'type' => BannerType::class,
            'display_until' => 'datetime',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
        );
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
        if (isset($activity->subject->type->site->company_id)) {
            $activity->company_id = $activity->subject->type->site->company_id;
        }
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function company(): HasOneThrough
    {
        return $this->hasOneThrough(
            Company::class,
            Site::class,
            'id', // Foreign key on sites table...
            'id', // Foreign key on companies table...
            'site_id', // Local key on banners table
            'company_id' // Local key on sites table
        );
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(SiteCategory::class, 'categorizable', 'site_categorizables');
    }
}
