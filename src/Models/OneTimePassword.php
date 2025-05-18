<?php

namespace Spatie\LaravelOneTimePasswords\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class OneTimePassword extends Model
{
    use MassPrunable;

    public $guarded = [];

    public function casts()
    {
        return [
            'origin_properties' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function generateFor(Model $model, int $expiresInMinutes = 10): self
    {
        return $model->oneTimePasswords()->create([
            'password' => Str::random(config('one-time-passwords.password_length')),
            'expires_at' => Carbon::now()->addMinutes($expiresInMinutes),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Validate the given password against this one-time password.
     */
    public function validate(string $password): bool
    {
        return $this->isValid() && $this->password === $password;
    }

    public function prunable(): Builder
    {
        return static::query()->wherePast('expires_at');
    }
}
