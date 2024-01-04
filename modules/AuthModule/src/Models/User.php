<?php

namespace Boilerplate\Auth\Models;

use Boilerplate\Auth\Enums\UsernameType;
use Boilerplate\Auth\Factories\UserFactory;
use Boilerplate\Auth\Models\Interfaces\Notifiable as NotifiableInterface;
use Boilerplate\Auth\Models\Traits\HasAvatar;
use Boilerplate\Auth\Models\Traits\InteractsWithChangeRequest;
use Boilerplate\Auth\Models\Traits\InteractsWithVerificationToken;
use Boilerplate\Auth\Models\Traits\ManagesOneTimePassword;
use Boilerplate\Auth\Observers\UserObserver;
use Boilerplate\Auth\Support\BypassCodeValidator;
use Boilerplate\Auth\Support\ValidatesPhone;
use Boilerplate\Sms\Notifications\Channels\SmsChannel;
use Boilerplate\SocialLogin\Models\Traits\HasSocialAccounts;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements
    MustVerifyEmail,
    HasMedia,
    NotifiableInterface,
    \Boilerplate\Auth\Contracts\User
{
    use HasRoles;
    use HasFactory;
    use HasAvatar;
    use Notifiable;
    use HasApiTokens;
    use ManagesOneTimePassword {
        hasValidOneTimePassword as traitHasValidOneTimePassword;
    }
    use InteractsWithChangeRequest;
    use InteractsWithVerificationToken;
    use ValidatesPhone;
    use BypassCodeValidator;
    use HasApiTokens;
    use InteractsWithVerificationToken;
    // use HasSocialAccounts;

    /**
     * The default guard to use
     * This will be used by laravel permission package to determine the guard to use
     *
     * @var string
     */
    protected $guard_name = 'api';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'phone_number_verification_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
        'onboarded_at' => 'datetime',
    ];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)
            ->withBlocked()
            ->first();
    }

    protected static function booted(): void
    {
        static::observe(UserObserver::class);

        static::addGlobalScope('blocked', function (Builder $query) {
            $query->whereNull('blocked_at');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function passwordReset(): HasOne
    {
        return $this->hasOne(PasswordReset::class, 'user_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('phone_number', 'LIKE', "%$search%");
        });
    }

    public function scopeWithBlocked(Builder $query): void
    {
        $query->withoutGlobalScope('blocked');
    }

    public function scopeOnlyBlocked(Builder $query): void
    {
        $query->withoutGlobalScope('blocked');
        $query->whereNotNull('blocked_at');
    }

    public function scopeOnlyNonOnBoarded(Builder $query): void
    {
        $query->whereNull('onboared_at');
    }

    public function scopeHasUsername(Builder $query, string $username): void
    {
        $query->where('email', $username);
        $query->orWhere('phone_number', $this->cleanPhoneNumber($username));
    }


    /*
    |--------------------------------------------------------------------------
    | Mutator methods
    |--------------------------------------------------------------------------
    */

    /**
     * Remove the plus (+) sign for every phone number
     */
    public function setPhoneNumberAttribute(?string $value): void
    {
        $this->attributes['phone_number'] = $value ? $this->cleanPhoneNumber($value) : $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor methods
    |--------------------------------------------------------------------------
    */

    /**
     * Converts the first character of each word in user's full name to uppercase
     */
    public function getFullNameAttribute(): string
    {
        return ucwords(implode(' ', [$this->first_name, $this->last_name]));
    }


    /**
     * Get verified phone number or email
     */
    public function getVerifiedAccountAttribute(): ?string
    {
        return $this->isEmailVerified() ? $this->email : $this->phone_number;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper methods
    |--------------------------------------------------------------------------
    */

    public function isValidEmailVerificationCode(?string $code): bool
    {
        /** on debug mode, allow bypass for token validation */
        if ($this->isUsingBypassCode($code)) {
            return true;
        }

        return $code === $this->email_verification_code;
    }

    public function isValidPhoneVerificationCode(?string $code): bool
    {
        /** on debug mode, allow bypass for token validation */
        if ($this->isUsingBypassCode($code)) {
            return true;
        }

        return $code === $this->phone_number_verification_code;
    }

    public function isVerified(): bool
    {
        return filled($this->email_verified_at) || filled($this->phone_number_verified_at);
    }

    public function isEmailVerified(): bool
    {
        return filled($this->email_verified_at);
    }

    public function isPhoneNumberVerified(): bool
    {
        return filled($this->phone_number_verified_at);
    }

    public function isBlocked(): bool
    {
        return filled($this->blocked_at);
    }

    public function hasEmail(): bool
    {
        return filled($this->email);
    }

    public function hasPhoneNumber(): bool
    {
        return filled($this->phone_number);
    }

    public function hasPassword(): bool
    {
        return filled($this->password);
    }

    public function isOnboarded(): bool
    {
        return filled($this->onboarded_at);
    }

    public function onboard(): void
    {
        if ($this->isOnboarded()) {
            return;
        }

        $this->onboarded_at = now();
        $this->save();
    }

    public function defaultAvatar(): string
    {
        return asset('/images/default-profile.png');
    }

    public function verifyEmailNow(): bool
    {
        return $this->update(['email_verified_at' => now()]);
    }

    public function verifyPhoneNumberNow(): bool
    {
        return $this->update(['phone_number_verified_at' => now()]);
    }

    public function isEmailPrimary(): bool
    {
        return $this->primary_username === UsernameType::EMAIL;
    }

    public function isPhonePrimary(): bool
    {
        return $this->primary_username === UsernameType::PHONE_NUMBER;
    }

    public function routeNotificationForSms($notification): ?string
    {
        return $this->phone_number ? $this->uncleanPhoneNumber($this->phone_number) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | One-Time-Password
    |--------------------------------------------------------------------------
    */

    /**
     * Validates if the one time password is correct.
     *
     * If otp is a valid one time password, it will invalidate the old
     * one and return true.
     */
    public function invalidateIfValidOneTimePassword(string $value): bool
    {
        if ($this->isUsingBypassCode($value)) {
            return true;
        }

        if ($this->traitHasValidOneTimePassword($value)) {
            $this->invalidateOneTimePassword();
            return true;
        }

        return false;
    }

    public function otpChannel(): string
    {
        return SmsChannel::class;
    }

    public function otpDestination(): string
    {
        return $this->uncleanPhoneNumber($this->phone_number);
    }
}
