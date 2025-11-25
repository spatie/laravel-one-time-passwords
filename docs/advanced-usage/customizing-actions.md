---
title: Customizing actions
weight: 1
---

The core functionality of this package is implemented in action classes. You can override the default behaviour by creating your own action classes and registering them in the `config/one-time-passwords.php` config file.

Here's an example where we override the `create_one_time_password` action to add custom logic after a one-time password is stored:

First, let's create the custom class.

```php
namespace App\Actions;

use Spatie\OneTimePasswords\Actions\CreateOneTimePasswordAction

class CustomCreateOneTimePasswordAction extends CreateOneTimePasswordAction
{
    public function execute(
        Authenticatable $user,
        ?int $expiresInMinutes = null,
        ?Request $request = null
    ): OneTimePassword {
        // Call the parent method to store the one-time password
        $oneTimePassword = parent::execute($user, $expiresInMinutes, $request);

        // Add your custom logic here
        
        // Don't forget to return the one-time password
        return $oneTimePassword;
    }
}
```

Next, register the custom action in the `config/one-time-passwords.php` config file:

```php
// config/one-time-passwords.php

return [
    // ...
    'actions' => [
        'create_one_time_password' => App\Actions\CustomCreateOneTimePasswordAction::class,
    ],
];
```

