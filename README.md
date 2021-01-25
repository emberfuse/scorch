# Citadel

## Introduction

Citadel is a frontend agnostic authentication backend implementation for Cratespace. Citadel registers the routes and controllers needed to implement all of Cratespace's authentication features, including login, registration, password reset, email verification, and more.

Citadel essentially takes the routes and controllers of Cratespace UI and offers them as a package that does not include a user interface. This allows you to still quickly scaffold the backend implementation of your application's authentication layer without being tied to any particular frontend opinions.

## Installation

To get started, install Citadel using the Composer package manager:

```bash
composer require thavarshan/citadel
```

Next, publish Citadel's resources using the `citadel:install` command:

```bash
php artisan citadel:install
```

This command will publish Citadel's actions, models, policies and service providers to your `app` directory by overwriting then or creating them a new. In addition, Citadel's configuration file and migrations will be published too.

Next, you should migrate your database:

```bash
php artisan migrate:fresh
```

Citadel publishes a modified `create_users_table` migration. To facilitate it's usage migrations have to be applied fresh.

#### The Citadel Service Provider

The `vendor:publish` command discussed above will also publish the `app/Providers/CitadelServiceProvider` file. You should ensure this file is registered within the `providers` array of your `app` configuration file.

This service provider registers the actions that Citadel published, instructing Citadel to use them when their respective tasks are executed by Citadel.

### Authentication

To get started, we need to instruct Citadel how to return our `login` view. Remember, Citadel is a headless authentication library.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Citadel\Citadel\View` class. Typically, you should call this method from the `boot` method of your `CitadelServiceProvider`:

```php
use Citadel\Citadel\View;

View::login('auth.login');
```

Citadel will take care of generating the `/login` route that returns this view. Your `login` template should include a form that makes a POST request to `/login`. The `/login` action expects a string email address / username and a `password`. The name of the email / username field should match the `username` value of the `citadel` configuration file.

If the login attempt is successful, Citadel will redirect you to the URI configured via the `home` configuration option within your `citadel` configuration file. If the login request was an XHR request, a `200` HTTP response will be returned.

If the request was not successful, the user will be redirect back to the login screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the `422` HTTP response.

#### Customizing User Authentication

Citadel will automatically retrieve and authenticate the user based on the provided credentials and the authentication guard that is configured for your application. However, you may sometimes wish to have full customization over how login credentials are authenticated and users are retrieved. Thankfully, Citadel allows you to easily accomplish this using the `AuthenticateUser` class.

The authentication process may be customized by modifying the `App\Actions\Citadel\AuthenticateUser` action.

### Registration

To begin implementing registration functionality, we need to instruct Citadel how to return our `register` view.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Citadel\Citadel\View` class. Typically, you should call this method from the `boot` method of your `CitadelServiceProvider`:

```php
use Citadel\Citadel\View;

View::register('auth.register');
```

Citadel will take care of generating the `/register` route that returns this view. Your `register` template should include a form that makes a POST request to `/register`. The `/register` action expects a string `name`, string email address / username, `password`, and `password_confirmation` fields. The name of the email / username field should match the `username` value of the `citadel` configuration file.

If the registration attempt is successful, Citadel will redirect you to the URI configured via the `home` configuration option within your `citadel` configuration file. If the login request was an XHR request, a `200` HTTP response will be returned.

If the request was not successful, the user will be redirect back to the registration screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the `422` HTTP response.

#### Customizing Registration

The user validation and creation process may be customized by modifying the `App\Actions\Citadel\CreateNewUser` action.

### Password Reset

#### Requesting A Password Reset Link

To begin implementing password reset functionality, we need to instruct Citadel how to return our "forgot password" view.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Citadel\Citadel\View` class. Typically, you should call this method from the `boot` method of your `CitadelServiceProvider`:

```php
use Citadel\Citadel\View;

View::requestPasswordReset('auth.forgot-password);
```

Citadel will take care of generating the `/forgot-password` route that returns this view. Your `forgot-password` template should include a form that makes a POST request to `/forgot-password`. The `/forgot-password` endpoint expects a string `email` field. The name of this field / database column should match the `email` value of the `citadel` configuration file.

If the password reset link request was successful, Citadel will redirect back to the `/forgot-password` route and send an email to the user with a secure link they can use to reset their password. If the request was an XHR request, a `200` HTTP response will be returned.

After being redirected back to the `/forgot-password` route after a successful request, the `status` session variable may be used to display the status of the password reset link request attempt:

```html
@if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('status') }}
    </div>
@endif
```

If the request was not successful, the user will be redirect back to the request password reset link screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the `422` HTTP response.

#### Resetting The Password

To finish implementing password reset functionality, we need to instruct Citadel how to return our "reset password" view.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Citadel\Citadel\View` class. Typically, you should call this method from the `boot` method of your `CitadelServiceProvider`:

```php
use Citadel\Citadel\View;

View::resetPassword('auth.reset-password', ['request' => $request]);
```

Citadel will take care of generating the route to display this view. Your `reset-password` template should include a form that makes a POST request to `/reset-password`. The `/reset-password` endpoint expects a string `email` field, a `password` field, a `password_confirmation` field, and a hidden field named `token` that contains the value of `request()->route('token')`. The name of the "email" field / database column should match the `email` value of the `citadel` configuration file.

If the password reset request was successful, Citadel will redirect back to the `/login` route so that the user can login with their new password. In addition a `status` session variable will be set so that you may display the successful status of the reset on your login screen:

```html
@if (session('status'))
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('status') }}
    </div>
@endif
```

If the request was an XHR request, a `200` HTTP response will be returned.

If the request was not successful, the user will be redirect back to the reset password screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the `422` HTTP response.

#### Customizing Password Resets

The password reset process may be customized by modifying the `App\Actions\Citadel\ResetUserPassword` action.

### Email Verification

After registration, you may wish for users to verify their email address before they continue accessing your application. To get started, ensure the `emailVerification` feature is enabled in your `citadel` configuration file's `features` array. Next, you should ensure that your `App\Models\User` class implements the `MustVerifyEmail` interface. This interface is already imported into this model for you.

Once these two setup steps have been completed, newly registered users will receive an email prompting them to verify their email address ownership. However, we need to inform Citadel how to display the email verification screen which informs the user that they need to go click the verification link in the email.

```php
use Citadel\Citadel\View;

View::verifyEmail('auth.verify-email');
```

Citadel will take care of generating the route to display this view when a user is redirected to the `/email/verify` endpoint by the built-in `verified` middleware.

Your `verify-email` template should include an informational message instructing the user to click the email verification link that was sent to their email address. You may optionally add a button to this template that triggers a POST request to `/email/verification-notification`. When this endpoint receives a request, a new verification email link will be emailed to the user, allowing the user to get a new verification link if the previous one was accidentally deleted or lost.

If the request to resend the verification link email was successful, Citadel will redirect back to the `/email/verify` endpoint with a `status` session variable, allowing you to display an informational message to the user informing them the operation was successful. If the request was an XHR request, a `202` HTTP response will be returned.

#### Protecting Routes

To specify that a route or group of routes requires that the user has previously verified their email address, you should attach Cratespace's built-in `verified` middleware to the route:

```php
Route::get('/home', function () {
    // ...
})->middleware(['verified']);
```

## Contributing

Thank you for considering contributing to Citadel! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Cratespace community is welcoming to all, please review and abide by the [Code of Conduct](.github/CODE_OF_CONDUCT.md).

## Security Vulnerabilities

Please review [our security policy](https://github.com/cratespace/citadel/security/policy) on how to report security vulnerabilities.

## License

Cratespace Citadel is open-sourced software licensed under the [MIT license](LICENSE.md).
