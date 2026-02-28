<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

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
}
