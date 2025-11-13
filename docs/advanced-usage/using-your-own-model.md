---
title: Using your own model
weight: 3
---

By using a custom model, you can change low-level behaviour of the package, such as setting a different table name or db connection, adding custom properties, or overriding methods.

## Step 1: Create a custom model

Create a new model that extends the `Spatie\OneTimePasswords\Models\OneTimePassword` model.

```php
namespace App\Models;

use Spatie\OneTimePasswords\Models\OneTimePassword as BaseOneTimePassword;

class CustomOneTimePassword extends BaseOneTimePassword
{
    // Add any custom properties or methods here
}
```

## Step 2: Update the configuration

Next, you need to update the `config/one-time-passwords.php` configuration file to use your custom model.

```php
// config/one-time-passwords.php

return [
    // ...
    'models' => [
        // The model used to store one-time passwords
        'model' => App\Models\CustomOneTimePassword::class,
    ],
];
```
