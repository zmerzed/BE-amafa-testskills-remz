<?php

namespace Boilerplate\Auth\Models;

use Boilerplate\Auth\Factories\PasswordResetFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PasswordReset
 *
 * @property int $user_id
 * @property string $token
 * @property Carbon $expires_at
 * @property Carbon|null $created_at
 * @property-read \Boilerplate\Auth\Models\User $user
 * @method static Builder|PasswordReset newModelQuery()
 * @method static Builder|PasswordReset newQuery()
 * @method static Builder|PasswordReset query()
 */
class PasswordReset extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'email',
        'token',
        'expires_at',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->token = random_int(10000, 99999);
            $model->expires_at = Carbon::now()->addRealMinutes(config('auth.passwords.users.expire'));
        });
    }

    protected static function newFactory(): PasswordResetFactory
    {
        return PasswordResetFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
