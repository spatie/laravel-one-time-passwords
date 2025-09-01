---
title: Configuring notifications
weight: 3
---

The package uses the included `OneTimePasswordNotification` notification to mail one-time passwords to users. 

By extending the notification, you can send the one-time password via other channels, such as SMS or Slack.

You can extend this notification to customize the content and appearance of the email. 

## Adding support for additional channels

To add support for additional channels, you can do so by creating a new notification class that extends the `Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification` class. In that custom class, you can add additional channels to the `via` method.

Before adding support for additional channels, please make sure to read the [Laravel documentation on customizing notification channels](https://laravel.com/docs/11.x/notifications).

Here's an example of how to add support for SMS (via Vonage as explained in the Laravel docs).

```php
namespace App\Notifications;

use Spatie\OneTimePasswords\Notifications\OneTimePasswordNotification;

class CustomOneTimePasswordNotification extends OneTimePasswordNotification
{
    public function via($notifiable): string|array
    {
        return ['vonage'];
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        // $this->oneTimePassword is an instance of the Spatie\OneTimePasswords\OneTimePassword model
    
        return (new VonageMessage)
            ->content("Your one-time login code is: {$this->oneTimePassword->password}");
    }
}
```

To complete the SMS routing, don't for get to add the `routeNotificationForVonage` to your `User` model (as explained in the Laravel docs).

```php
namespace App\Models;
 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
 
class User extends Authenticatable
{
    use Notifiable;
 
    /**
     * Route notifications for the Vonage channel.
     */
    public function routeNotificationForVonage(Notification $notification): string
    {
        return $this->phone_number;
    }
}
```

Finally, you need to update the `config/one-time-passwords.php` configuration file to use your custom notification class.

```php
// config/one-time passwords.php

return [
    // ...
    'notification' =>  => App\Notifications\CustomOneTimePasswordNotification::class
];
```

### Styling the mail notification

The easiest way to customize the looks of the mail notification is by publishing the views.

```bash
php artisan vendor:publish --tag=one-time-passwords-views
```

This will publish the views to `resources/views/vendor/one-time-passwords/mail.blade.php`. You can customize this view to change the appearance of the notification.

