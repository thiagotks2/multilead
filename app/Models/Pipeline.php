<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pipeline extends Model
{
    /** @use HasFactory<\Database\Factories\PipelineFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('pipeline')
            ->tap(function (\Spatie\Activitylog\Models\Activity $activity) {
                if (isset($activity->subject->company_id)) {
                    $activity->company_id = $activity->subject->company_id;
                }
            });
    }

    protected $fillable = [
        'company_id',
        'name',
        'is_default',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class)->orderBy('order_column');
    }
}
