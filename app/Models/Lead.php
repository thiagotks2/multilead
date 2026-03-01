<?php

namespace App\Models;

use App\Enums\LeadMedium;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('lead')
            ->tap(function (\Spatie\Activitylog\Models\Activity $activity) {
                if (isset($activity->subject->company_id)) {
                    $activity->company_id = $activity->subject->company_id;
                }
            });
    }

    protected $fillable = [
        'company_id',
        'lead_source_id',
        'user_id',
        'customer_id',
        'pipeline_stage_id',
        'name',
        'email',
        'phone',
        'message',
        'notes',
        'medium',
    ];

    protected function casts(): array
    {
        return [
            'medium' => LeadMedium::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }
}
