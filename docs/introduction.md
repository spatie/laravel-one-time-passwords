---
title: Introduction
weight: 1
---

Using this package, you can securely create and consume one-time passwords. By default, a one-time password is a number
of six digits long that will be sent via a mail notification. This notification can be extended so it can be sent via other channels, like SMS.

The package ships with a Livewire
component to allow users to login using a one-time password.

![image](/docs/laravel-one-time-passwords/v1/images/form-email.png)

![image](/docs/laravel-one-time-passwords/v1/images/form-code.png)

Alternatively, you can to build the one-time password login flow you want with the easy-to-use methods the package provides.

Here's how you would send a one-time password to a user

```php
// send a mail containing a one-time password

$user->sendOneTimePassword();
```

This is what the notification mail looks like:

![image](/docs/laravel-one-time-passwords/v1/images/otp-notification.png)

Here's how you would try to log in a user using a one-time password.

```php
use Spatie\OneTimePasswords\Enums\ConsumeOneTimePasswordResult;

$result = $user->attemptLoginUsingOneTimePassword($oneTimePassword);

if ($result->isOk()) {
     // it is best practice to regenerate the session id after a login   
     $request->session()->regenerate();
              
     return redirect()->intended('dashboard');
}

return back()->withErrors([
    'one_time_password' => $result->validationMessage(),
])->onlyInput('one_time_password');
```

The package tries to make one-time passwords as secure as can be by:

- letting them expire in a short timeframe (2 minutes by default)
- only allowing to consume a one-time password on the same IP and user agent as it was generated

All behavior is implemented in action classes that can be modified to your liking.
