---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-one-time-passwords
```

## Migrating the database

This package can store one-time passwords in the database. You can create the `one_time_passwords` table by publishing and running the migrations.

```bash
php artisan vendor:publish --tag="one-time-passwords-migrations"
php artisan migrate
```

## Preparing your model

You should let your `User` model use the `HasOneTimePasswords` trait.

```php
namespace App\Models;

use Spatie\OneTimePasswords\Models\Concerns\HasOneTimePasswords;

class User
{
    use HasOneTimePasswords;
    
    // ...
}
```

## Deleting expired one-time passwords

This package uses [the `MassPrunable` trait provided by Laravel](https://laravel.com/docs/12.x/eloquent#pruning-models).

To delete expired one-time password, you can add the `model:prune` command to your schedule.

Here's an example where expired one-time passwords are deleted daily.

```php
use Spatie\OneTimePasswords\Models\OneTimePassword;

Schedule::command('model:prune', [
    '--model' => [OneTimePassword::class],
])->daily();
```

## Publishing the config file

Optionally, you can publish the `one-time-passwords` config file with this command.

```bash
php artisan vendor:publish --tag="one-time-passwords-config"
```

This is the content of the published config file:

```php
return [
    /*
     * One time passwords should be consumed within this number of minutes
     */
    'default_expires_in_minutes' => 2,

    /*
     * When this setting is active, we'll delete all previous one-time passwords for
     * a user when generating a new one
     */
    'only_one_active_one_time_password_per_user' => true,

    /*
     * When this option is active, we'll try to ensure that the one-time password can only
     * be consumed on the platform where it was requested on
     */
    'enforce_same_origin' => true,

    /*
     * This class is responsible to enforce that the one-time password can only be consumed on
     * the platform it was requested on.
     *
     * If you do not wish to enforce this, set this value to
     * Spatie\OneTimePasswords\Support\OriginInspector\DoNotEnforceOrigin
     */
    'origin_enforcer' => Spatie\OneTimePasswords\Support\OriginInspector\DefaultOriginEnforcer::class,

    /*
     * This class generates a random password
     */
    'password_generator' => Spatie\OneTimePasswords\Support\PasswordGenerators\NumericOneTimePasswordGenerator::class,

    /*
     * By default, the password generator will create a password with
     * this number of digits
     */
    'password_length' => 6,

    'redirect_successful_authentication_to' => '/dashboard',

    /*
     * These values are used to rate limit the number of attempts
     * that may be made to consume a one-time password.
     */
    'rate_limit_attempts' => [
        'max_attempts_per_user' => 5,
        'time_window_in_seconds' => 60,
    ],

    /*
     * The model uses to store one-time passwords
     */
    'model' => Spatie\OneTimePasswords\Models\OneTimePassword::class,

    /*
     * The notification used to send a one-time password to a user
     */
    'notification' => Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification::class,

    /*
     * These class are responsible for performing core tasks regarding one-time passwords.
     * You can customize them by creating a class that extends the default, and
     * by specifying your custom class name here.
     */
    'actions' => [
        'create_one_time_password' => Spatie\OneTimePasswords\Actions\CreateOneTimePasswordAction::class,
        'consume_one_time_password' => Spatie\OneTimePasswords\Actions\ConsumeOneTimePasswordAction::class,
    ],
];
```
