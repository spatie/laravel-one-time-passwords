<?php

namespace Spatie\LaravelOneTimePasswords\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function invalidModel(string $model): self
    {
        return new self("The configured model `{$model}` is not a valid model because it does not extend or is `Spatie\LaravelOneTimePasswords\Models\OneTimePassword`.");
    }

    public static function invalidNotification(mixed $notificationClass): self
    {
        return new self("The configured notification `{$notificationClass}` is not a valid notification class because it does not extend `Spatie\LaravelOneTimePasswords\Notifications\OneTimePasswordNotification`.");
    }

    public static function invalidAction(string $actionName): self
    {
        return new self("The configured action for `{$actionName}` is missing or not a valid class string.");
    }
}
