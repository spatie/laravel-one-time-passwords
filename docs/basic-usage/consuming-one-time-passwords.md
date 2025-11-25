---
title: Consuming one-time passwords
weight: 4
---

The package provides two methods to consume one-time passwords: `attemptLoginUsingOneTimePassword` and `consumeOneTimePassword`. Both of them will verify the given one-time password and return an instance of the `ConsumeOneTimePasswordResult` enum. If the one-time password is correct, the underlying `OneTimePassword` model for that password will be deleted, ensure that a one-time password can only be used once.

By default, a one-time password can only be used on the same origin it was created on. This is to prevent a one-time password from being used on a different device or browser. You can read more about this in the [Enforcing Origin](/docs/laravel-one-time-passwords/v2/configuring-security/enforcing-origin) section.

## Consuming one-time passwords

When implementing your login flow using one-time passwords, you can use the `attemptLoginUsingOneTimePassword` method which will verify the given one-time password and log in the user.

Here's an example:

```php
use Spatie\OneTimePasswords\Enums\ConsumeOneTimePasswordResult;

// $result is an instance of the ConsumeOneTimePasswordResult enum.
$result = $user->attemptLoginUsingOneTimePassword($oneTimePassword, remember: false);

if ($result->isOk()) {
     // it is best practice to regenerate the session id after a login   
     $request->session()->regenerate();
              
     return redirect()->intended('dashboard');
}

return back()->withErrors([
    'one_time_password' => $result->validationMessage(),
])->onlyInput('one_time_password');
```

Alternatively, you can use the `consumeOneTimePassword`. Which will do the same as `attemptLoginUsingOneTimePassword` except it won't log in the user.

```php
$result = $user->consumeOneTimePassword($oneTimePassword);
```

## Inspecting the result

Both `attemptLoginUsingOneTimePassword` and `consumeOneTimePassword` will return an instance of the `ConsumeOneTimePasswordResult` enum which has these cases:

- `Ok`: The one-time password was correct.
- `NoOneTimePasswordsFound`: The user has no one-time passwords.
- `IncorrectOneTimePassword`: The one-time password was incorrect.
- `DifferentOrigin`: The one-time password was created from a different origin.
- `OneTimePasswordExpired`: The one-time password has expired.
- `RateLimitExceeded`: The user has exceeded the rate limit for one-time passwords.
