<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PipelineStage extends Model
{
    /** @use HasFactory<\Database\Factories\PipelineStageFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('pipeline_stage')
            ->tap(function (\Spatie\Activitylog\Models\Activity $activity) {
                if (isset($activity->subject->pipeline->company_id)) {
                    $activity->company_id = $activity->subject->pipeline->company_id;
                }
            });
    }

    protected $fillable = [
        'pipeline_id',
        'name',
        'color',
        'is_default',
        'is_visible',
        'order_column',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'pipeline_stage_id');
    }
}
