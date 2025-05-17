<?php

namespace Spatie\LaravelOneTimePasswords\Support;

use Spatie\LaravelOneTimePasswords\Exceptions\InvalidActionClass;
use Spatie\LaravelOneTimePasswords\Exceptions\InvalidConfig;
use Spatie\LaravelOneTimePasswords\Models\OneTimePassword;
use Spatie\LaravelOneTimePasswords\Notifications\OneTimePasswordNotification;

class Config
{
    /**
     * @return class-string<OneTimePassword>
     */
    public static function oneTimePasswordModel(): string
    {
        $modelClass = config('one-time-passwords.model');

        if (! is_string($modelClass) || ! is_a($modelClass, OneTimePassword::class, true)) {
            throw InvalidConfig::invalidModel(is_scalar($modelClass) ? (string) $modelClass : gettype($modelClass));
        }

        return $modelClass;
    }

    public static function oneTimePasswordNotificationClass(): string
    {
        $notificationClass = config('one-time-passwords.notification');

        if (! is_a($notificationClass, OneTimePasswordNotification::class, true)) {
            throw InvalidConfig::invalidNotification($notificationClass);
        }

        return $notificationClass;
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $actionBaseClass
     * @return T
     */
    public static function getAction(string $actionName, string $actionBaseClass)
    {
        $actionClass = self::getActionClass($actionName, $actionBaseClass);

        return app($actionClass);
    }

    public static function getActionClass(string $actionName, string $actionBaseClass): string
    {
        $actionClass = config("one-time-passwords.actions.{$actionName}");

        self::ensureValidActionClass($actionName, $actionBaseClass, $actionClass);

        return $actionClass;
    }

    protected static function ensureValidActionClass(string $actionName, string $actionBaseClass, string $actionClass): void
    {
        if (! is_a($actionClass, $actionBaseClass, true)) {
            throw InvalidActionClass::make($actionName, $actionBaseClass, $actionClass);
        }
    }
}
