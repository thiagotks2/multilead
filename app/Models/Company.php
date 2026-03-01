<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('company')
            ->tap(function (\Spatie\Activitylog\Models\Activity $activity) {
                if (isset($activity->subject->id)) {
                    $activity->company_id = $activity->subject->id; // For Company, the ID itself is the company_id
                }
            });
    }

    protected $fillable = [
        'name',
        'fantasy_name',
        'document_type',
        'document_number',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'active',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
        'active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function pipelines(): HasMany
    {
        return $this->hasMany(Pipeline::class);
    }
}
