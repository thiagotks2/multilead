<?php

namespace App\Models;

use App\Enums\LeadMedium;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;
use App\Models\LeadSource;
use App\Models\User;
use App\Models\PipelineStage;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory, SoftDeletes;

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
