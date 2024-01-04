<?php

namespace Boilerplate\Auth\Models;

use Boilerplate\Auth\Support\BypassCodeValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Hash;

/**
 * App\Models\ChangeRequest
 *
 * @property int $id
 * @property string $changeable_type
 * @property int $changeable_id
 * @property string|null $from
 * @property string|null $to
 * @property string $field_name
 * @property string|null $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model $changeable
 *
 * @method static Builder|ChangeRequest newModelQuery()
 * @method static Builder|ChangeRequest newQuery()
 * @method static Builder|ChangeRequest query()
 */
class ChangeRequest extends Model
{
    use BypassCodeValidator;

    protected $fillable = [
        'from',
        'to',
        'token',
        'field_name',
    ];

    public function changeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isTokenValid($token): bool
    {
        if ($this->isUsingBypassCode($token)) {
            return true;
        }

        return Hash::check($token, $this->token);
    }
}
