<?php

namespace App\Modules\Websites\Models;

use App\Modules\Identity\Models\Company;
use App\Modules\Websites\Enums\CategoryType;
use Database\Factories\SiteCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SiteCategory extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function newFactory(): SiteCategoryFactory
    {
        return SiteCategoryFactory::new();
    }

    protected $fillable = [
        'site_id',
        'type',
        'name',
        'slug',
        'description',
        'seo_settings',
        'scripts',
    ];

    protected function casts(): array
    {
        return [
            'type' => CategoryType::class,
            'seo_settings' => 'array',
            'scripts' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('site_category');
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        if ($this->site_id && ! $this->relationLoaded('site')) {
            $this->load(['site']);
        }

        if ($this->site) {
            $activity->company_id = $this->site->company_id;
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
            'site_id', // Local key on categories table
            'company_id' // Local key on sites table
        );
    }

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(SitePost::class, 'categorizable', 'site_categorizables');
    }
}
