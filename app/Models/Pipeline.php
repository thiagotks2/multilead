<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;
use App\Models\PipelineStage;

class Pipeline extends Model
{
    /** @use HasFactory<\Database\Factories\PipelineFactory> */
    use HasFactory, SoftDeletes;

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
