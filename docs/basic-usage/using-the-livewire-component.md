---
title: Using the Livewire component
weight: 5
---

The package provides a Livewire component that can create and consume one-time passwords. Here's how you can use it in your view.

```html
<livewire:one-time-password>
```

It will render a form asking for the user's email address and a button to send the one-time password. After the user submits the form, it will send a one-time password to the provided email address. 

![image](/docs/laravel-one-time-passwords/v2/images/form-email.png)

The component will then display a form asking for the one-time password and a button to verify it. After the user submits the form, it will verify the one-time password and log the user in if the verification is successful.

![image](/docs/laravel-one-time-passwords/v2/images/form-code.png)

### Only consuming a one-time password

If you want to use the component only for consuming a one-time password, you can pass the `email` prop to the component. 

```html
<livewire:one-time-password email="johndoe@example.com">
```

This will skip the email address input and directly show the form for entering the one-time password.

### Redirecting after successful authentication

By default, authenticated users will be redirected to the url specified in the `redirect_successful_authentication_to` property of the `one-time-passwords` config file.

To customize the redirect URL, you can also pass an url to the `redirect-to` prop.

```html
<livewire:one-time-password :redirect-to="route('home')">
```

### Using the Flux OTP input component

When [Flux](https://fluxui.dev) is installed, the Livewire component will automatically use the [Flux OTP input component](https://fluxui.dev/components/otp-input), which is much nicer than the default text input.

You can install Flux by following the instructions on their website: [https://fluxui.dev](https://fluxui.dev)

Once Flux is installed, the package will automatically detect it and use the Flux OTP input component instead of the default text input.

![image](/docs/laravel-one-time-passwords/v2/images/flux.jpg)

## Customizing the styling

You can customize the styling of the component by publishing the package's assets. To do this, run the following command:

```bash
php artisan vendor:publish --tag=one-time-passwords-views
```

This will publish the package's views to the `resources/views/vendor/one-time-passwords` directory. You can then customize the views as needed.


### Customizing the component

To have full control over the component, you can create your own Livewire component, and let it extend `Spatie\OneTimePasswords\Livewire\OneTimePasswordComponent`.

This way, you can override any methods or properties you want.
