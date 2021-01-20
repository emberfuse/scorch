# Citadel

## Introduction

Citadel is a frontend agnostic authentication backend implementation for Laravel. Citadel registers the routes and controllers needed to implement all of Laravel's authentication features, including login, registration, password reset, email verification, and more.

Citadel essentially takes the routes and controllers of Laravel UI and offers them as a package that does not include a user interface. This allows you to still quickly scaffold the backend implementation of your application's authentication layer without being tied to any particular frontend opinions.

## Installation

To get started, install Citadel using the Composer package manager:

```bash
composer require thavarshan/citadel
```

Next, publish Citadel's resources using the `vendor:publish` command:

```bash
php artisan vendor:publish --provider="Citadel\Providers\CitadelServiceProvider"
```

This command will publish Citadel's actions to your `app/Actions` directory, which will be created if it does not exist. In addition, Citadel's configuration file and migrations will be published.

Next, you should migrate your database:

```bash
php artisan migrate
```

### The Citadel Service Provider

The `vendor:publish` command discussed above will also publish the `App\Providers\CitadelServiceProvider` class. You should ensure this class is registered within the providers array of your application's `config/app.php` configuration file.

The Citadel service provider registers the actions that Citadel published and instructs Citadel to use them when their respective tasks are executed by Citadel.

### Citadel Features

The fortify configuration file contains a features configuration array. This array defines which backend routes / features Citadel will expose by default.

## Authentication

To get started, we need to instruct Citadel how to return our "login" view. Remember, Citadel is a headless authentication library.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Citadel\Citadel\Application` class. Typically, you should call this method from the boot method of your application's `App\Providers\CitadelServiceProvider` class. Citadel will take care of defining the /login route that returns this view:

```php
use Citadel\Citadel\Application as Citadel;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    Citadel::loginView(fn () => view('auth.login'));

    // ...
}
```

Your login template should include a form that makes a POST request to `/login`. The `/login` endpoint expects a string email address / username and a `password`. The name of the email / username field should match the `username` value within the `config/citadel.php` configuration file. In addition, a boolean `remember` field may be provided to indicate that the user would like to use the "remember me" functionality provided by Laravel.

If the login attempt is successful, Fortify will redirect you to the URI configured via the `home` configuration option within your application's `citadel` configuration file. If the login request was an XHR request, a 200 HTTP response will be returned.

If the request was not successful, the user will be redirected back to the login screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the 422 HTTP response.
