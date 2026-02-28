<?php

namespace App\Models;

use App\Enums\SiteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'visual_settings',
        'default_meta_title',
        'default_meta_description',
        'default_meta_keywords',
        'canonical_url',
        'scripts_header',
        'scripts_body',
        'scripts_footer',
        'mail_default_recipient',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'mail_from_address',
        'mail_from_name',
        'privacy_policy_text',
        'company_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SiteStatus::class,
            'visual_settings' => 'array',
            'smtp_password' => 'encrypted',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
