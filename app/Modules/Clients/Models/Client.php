<?php

namespace App\Modules\Clients\Models;

use App\Modules\Identity\Models\Company;
use App\Modules\Identity\Models\User;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
        'user_id',
        'company_id',
        'address',
        'profile_data',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'address' => AsArrayObject::class,
            'profile_data' => AsArrayObject::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('client');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        if (isset($activity->subject->company_id)) {
            $activity->company_id = $activity->subject->company_id;
        }
    }

    protected static function newFactory(): ClientFactory
    {
        return ClientFactory::new();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
