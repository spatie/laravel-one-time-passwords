---
title: Creating one-time passwords
weight: 2
---

The package has methods to create and send one-time passwords. These methods will create a row in the `one_time_passwords` table.

By default, the one-time password will be six digits long, but you can [configure its format](/docs/laravel-one-time-passwords/v2/configuring-security/enforcing-origin). One-time passwords have a default expiry time of 2 minutes, but the expiration time [can be configured as well](/docs/laravel-one-time-passwords/v2/configuring-security/setting-default-expiration-time).

When creating a one-time password for the user, all older one-time passwords for that user will be deleted.

## Creating and sending a one-time password

```php
$user->sendOneTimePassword();
```

This will create a one-time password and send it to the user's email using the `OneTimePasswordNotification`-notification.

## Only creating a one-time password

To create a one-time password without actually sending it to the user, you can call the `createOneTimePassword` method.

```php
$oneTimePasswordModel = $user->createOneTimePassword();
```

This method will return an instance of a newly created `OneTimePassword` model. 


