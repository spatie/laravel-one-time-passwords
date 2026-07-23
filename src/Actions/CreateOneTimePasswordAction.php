<?php

namespace Spatie\OneTimePasswords\Actions;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\OneTimePasswords\Models\Concerns\HasOneTimePasswords;
use Spatie\OneTimePasswords\Models\OneTimePassword;
use Spatie\OneTimePasswords\Support\OriginInspector\OriginEnforcer;
use Spatie\OneTimePasswords\Support\PasswordGenerators\OneTimePasswordGenerator;

class CreateOneTimePasswordAction
{
    public function __construct(
        protected OriginEnforcer $originEnforcer,
        protected OneTimePasswordGenerator $passwordGenerator,
    ) {}

    /**
     * @param  Authenticatable&HasOneTimePasswords  $user
     */
    public function execute(
        Authenticatable $user,
        ?int $expiresInMinutes = null,
        ?Request $request = null,
        ?Model $verifiable = null,
        ?string $type = null
    ): OneTimePassword {

        if (config('one-time-passwords.only_one_active_one_time_password_per_user')) {
            $this->deleteOldOneTimePasswords($user);
        }

        return $this->createNewOneTimePassword($expiresInMinutes, $user, $request, $verifiable, $type);
    }

    /**
     * @param  Authenticatable&HasOneTimePasswords  $user
     */
    protected function createNewOneTimePassword(
        ?int $expiresInMinutes,
        Authenticatable $user,
        ?Request $request,
        ?Model $verifiable = null,
        ?string $type = null
    ): OneTimePassword {
        $expiresInMinutes ??= config('one-time-passwords.default_expires_in_minutes');

        return $user->oneTimePasswords()->create([
            'password' => $this->passwordGenerator->generate(),
            'expires_at' => Carbon::now()->addMinutes($expiresInMinutes),
            'origin_properties' => $this->originEnforcer->gatherProperties($request ?? request()),
            'verifiable_type' => $verifiable !== null ? $verifiable::class : null,
            'verifiable_id' => $verifiable?->getKey(),
            'type' => $type,
        ]);
    }

    /**
     * @param  Authenticatable&HasOneTimePasswords  $user
     */
    protected function deleteOldOneTimePasswords(Authenticatable $user): void
    {
        $user->oneTimePasswords()->delete();
    }
}
