# Scorch

## Introduction

Scorch is a frontend agnostic authentication backend implementation for Emberfuse. Scorch registers the routes and controllers needed to implement all of Emberfuse's authentication features, including login, registration, password reset, email verification, and more.

Scorch essentially takes the routes and controllers of Emberfuse UI and offers them as a package that does not include a user interface. This allows you to still quickly scaffold the backend implementation of your application's authentication layer without being tied to any particular frontend opinions.

## Installation

To get started, install Scorch using the Composer package manager:

```bash
composer require emberfuse/scorch
```

Next, publish Scorch's resources using the `scorch:install` command:

```bash
php artisan scorch:install
```

This command will publish Scorch's actions, models, policies and service providers to your `app` directory by overwriting then or creating them a new. In addition, Scorch's configuration file and migrations will be published too.

Next, you should migrate your database:

```bash
php artisan migrate:fresh
```

Scorch publishes a modified `create_users_table` migration. To facilitate it's usage migrations have to be applied fresh.

#### The Scorch Service Provider

The `vendor:publish` command discussed above will also publish the `app/Providers/ScorchServiceProvider` file. You should ensure this file is registered within the `providers` array of your `app` configuration file.

This service provider registers the actions that Scorch published, instructing Scorch to use them when their respective tasks are executed by Scorch.

### Authentication

To get started, we need to instruct Scorch how to return our `login` view. Remember, Scorch is a headless authentication library.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Scorch\Scorch\View` class. Typically, you should call this method from the `boot` method of your `ScorchServiceProvider`:

```php
use Emberfuse\Scorch\Scorch\View;

View::login('auth.login');
```

Scorch will take care of generating the `/login` route that returns this view. Your `login` template should include a form that makes a POST request to `/login`. The `/login` action expects a string email address / username and a `password`. The name of the email / username field should match the `username` value of the `scorch` configuration file.

If the login attempt is successful, Scorch will redirect you to the URI configured via the `home` configuration option within your `scorch` configuration file. If the login request was an XHR request, a `200` HTTP response will be returned.

If the request was not successful, the user will be redirect back to the login screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the `422` HTTP response.

#### Customizing User Authentication

Scorch will automatically retrieve and authenticate the user based on the provided credentials and the authentication guard that is configured for your application. However, you may sometimes wish to have full customization over how login credentials are authenticated and users are retrieved. Thankfully, Scorch allows you to easily accomplish this using the `AuthenticateUser` class.

The authentication process may be customized by modifying the `App\Actions\Scorch\AuthenticateUser` action.

### Registration

To begin implementing registration functionality, we need to instruct Scorch how to return our `register` view.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Scorch\Scorch\View` class. Typically, you should call this method from the `boot` method of your `ScorchServiceProvider`:

```php
use Emberfuse\Scorch\Scorch\View;

View::register('auth.register');
```

Scorch will take care of generating the `/register` route that returns this view. Your `register` template should include a form that makes a POST request to `/register`. The `/register` action expects a string `name`, string email address / username, `password`, and `password_confirmation` fields. The name of the email / username field should match the `username` value of the `scorch` configuration file.

If the registration attempt is successful, Scorch will redirect you to the URI configured via the `home` configuration option within your `scorch` configuration file. If the login request was an XHR request, a `200` HTTP response will be returned.

If the request was not successful, the user will be redirect back to the registration screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the `422` HTTP response.

#### Customizing Registration

The user validation and creation process may be customized by modifying the `App\Actions\Scorch\CreateNewUser` action.

### Password Reset

#### Requesting A Password Reset Link

To begin implementing password reset functionality, we need to instruct Scorch how to return our "forgot password" view.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Scorch\Scorch\View` class. Typically, you should call this method from the `boot` method of your `ScorchServiceProvider`:

```php
use Emberfuse\Scorch\Scorch\View;

View::requestPasswordReset('auth.forgot-password);
```

Scorch will take care of generating the `/forgot-password` route that returns this view. Your `forgot-password` template should include a form that makes a POST request to `/forgot-password`. The `/forgot-password` endpoint expects a string `email` field. The name of this field / database column should match the `email` value of the `scorch` configuration file.

If the password reset link request was successful, Scorch will redirect back to the `/forgot-password` route and send an email to the user with a secure link they can use to reset their password. If the request was an XHR request, a `200` HTTP response will be returned.

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

To finish implementing password reset functionality, we need to instruct Scorch how to return our "reset password" view.

All of the authentication view's rendering logic may be customized using the appropriate methods available via the `Scorch\Scorch\View` class. Typically, you should call this method from the `boot` method of your `ScorchServiceProvider`:

```php
use Emberfuse\Scorch\Scorch\View;

View::resetPassword('auth.reset-password', ['request' => $request]);
```

Scorch will take care of generating the route to display this view. Your `reset-password` template should include a form that makes a POST request to `/reset-password`. The `/reset-password` endpoint expects a string `email` field, a `password` field, a `password_confirmation` field, and a hidden field named `token` that contains the value of `request()->route('token')`. The name of the "email" field / database column should match the `email` value of the `scorch` configuration file.

If the password reset request was successful, Scorch will redirect back to the `/login` route so that the user can login with their new password. In addition a `status` session variable will be set so that you may display the successful status of the reset on your login screen:

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

The password reset process may be customized by modifying the `App\Actions\Scorch\ResetUserPassword` action.

### Email Verification

After registration, you may wish for users to verify their email address before they continue accessing your application. To get started, ensure the `emailVerification` feature is enabled in your `scorch` configuration file's `features` array. Next, you should ensure that your `App\Models\User` class implements the `MustVerifyEmail` interface. This interface is already imported into this model for you.

Once these two setup steps have been completed, newly registered users will receive an email prompting them to verify their email address ownership. However, we need to inform Scorch how to display the email verification screen which informs the user that they need to go click the verification link in the email.

```php
use Emberfuse\Scorch\Scorch\View;

View::verifyEmail('auth.verify-email');
```

Scorch will take care of generating the route to display this view when a user is redirected to the `/email/verify` endpoint by the built-in `verified` middleware.

Your `verify-email` template should include an informational message instructing the user to click the email verification link that was sent to their email address. You may optionally add a button to this template that triggers a POST request to `/email/verification-notification`. When this endpoint receives a request, a new verification email link will be emailed to the user, allowing the user to get a new verification link if the previous one was accidentally deleted or lost.

If the request to resend the verification link email was successful, Scorch will redirect back to the `/email/verify` endpoint with a `status` session variable, allowing you to display an informational message to the user informing them the operation was successful. If the request was an XHR request, a `202` HTTP response will be returned.

##### Resending Email Verification Links

If you wish, you may add a button to your application's `verify-email` template that triggers a POST request to the `/email/verification-notification` endpoint. When this endpoint receives a request, a new verification email link will be emailed to the user, allowing the user to get a new verification link if the previous one was accidentally deleted or lost.

If the request to resend the verification link email was successful, Scorch will redirect the user back to the `/email/verify` endpoint with a `status` session variable, allowing you to display an informational message to the user informing them the operation was successful. If the request was an XHR request, a 202 HTTP response will be returned:

```html
@if (session('status') == 'verification-link-sent')
    <div class="mb-4 font-medium text-sm text-green-600">
        A new email verification link has been emailed to you!
    </div>
@endif
```

#### Protecting Routes

To specify that a route or group of routes requires that the user has previously verified their email address, you should attach Emberfuse's built-in `verified` middleware to the route:

```php
Route::get('/home', function () {
    // ...
})->middleware(['verified']);
```

### User Profile

The logic executed to satisfy profile update requests can be found in an action class within your application. Specifically, the `App\Actions\Scorch\UpdateUserProfile` class will be invoked when the user updates their profile. This action is responsible for validating the input and updating the user's profile information.

Therefore, any customizations you wish to make to your application's management of this information should be made in this class. When invoked, the action receives the currently authenticated `$user` and an array of `$data` that contains all of the input from the incoming request, including the updated profile photo if applicable.

All of the user profile view rendering logic may be customized using the appropriate methods available via the `Scorch\Scorch\View` class. Typically, you should call this method from the `boot` method of your `ScorchServiceProvider`:

```php
use Emberfuse\Scorch\Scorch\View;

View::userProfile('users.show');
```

Scorch will take care of generating the `/user/profile` route that returns this view. Your `user profile` template should include a form that makes a PUT request to `/user/profile`. The `/user/profile` endpoint expects a string `email` field. The name of this field / database column should match the `email` value of the `scorch` configuration file.

If the update request was successful, Scorch will redirect back to the `/user/profile` route. If the request was an XHR request, a `204` HTTP response will be returned.

If the request was not successful, the user will be redirect back to the user profile screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with the `422` HTTP response.

#### Customizing User Profile Update Action

The user profile update process may be customized by modifying the `App\Actions\Scorch\UpdateUserProfile` action.

### Profile Photos

Scorch's profile photo functionality is supported by the `Scorch\Models\Traits\HasProfilePhoto` trait that is automatically attached to your `App\Models\User` class during Scorch's installation.

This trait contains methods such as `updateProfilePhoto`, `getProfilePhotoUrlAttribute`, `defaultProfilePhotoUrl`, and `profilePhotoDisk` which may all be overwritten by your own `App\Models\User` class if you need to customize their behavior. You are encouraged to read through the source code of this trait so that you have a full understanding of the features it is providing to your application.

The `updateProfilePhoto` method is the primary method used to store profile photos and is called by your application's `App\Actions\Scorch\UpdateUserProfile` action class.

### Account Deletion

The profile management screen can also include an action panel that allows the user to delete their application account. When the user chooses to delete their account, the `App\Actions\Scorch\DeleteUser` action class will be invoked. You are free to customize your application's account deletion logic within this class.

### Password Update

Like most of Scorch's features, the underlying logic used to implement the feature may be customized by modifying a corresponding action class.

The `App\Actions\Scorch\UpdateUserPassword` class will be invoked when the user updates their password. This action is responsible for validating the input and updating the user's password.

Scorch utilizes a custom `Scorch\Rules\PasswordRule` validation rule object. This object allows you to easily customize the password requirements for your application. By default, the rule requires a password that is at least eight characters in length. However, you may use the following methods to customize the password's requirements:

```php
use Emberfuse\Scorch\Rules\PasswordRule;

// Require at least 10 characters...
(new PasswordRule())->length(10);

// Require at least one uppercase character...
(new PasswordRule())->requireUppercase();

// Require at least one numeric character...
(new PasswordRule())->requireNumeric();

// Require at least one special character...
(new PasswordRule())->requireSpecialCharacter();
```

Of course, these methods may be chained to define the password validation rules for your application:

```php
(new PasswordRule())->length(10)->requireSpecialCharacter();
```

### Password Confirmation

While building your application, you may occasionally have actions that should require the user to confirm their password before the action is performed. Typically, these routes are protected by built-in `password.confirm` middleware.

To begin implementing password confirmation functionality, we need to instruct Scorch how to return our application's "password confirmation" view.

```php
use Emberfuse\Scorch\Scorch\View;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    View::confirmPassword('auth.confirm-password');
}
```

Scorch will take care of defining the `/user/confirm-password` endpoint that returns this view. Your confirm-password template should include a form that makes a POST request to the `/user/confirm-password` endpoint. The `/user/confirm-password` endpoint expects a password field that contains the user's current password.

If the password matches the user's current password, Scorch will redirect the user to the route they were attempting to access. If the request was an XHR request, a 201 HTTP response will be returned.

If the request was not successful, the user will be redirected back to the confirm password screen and the validation errors will be available to you via the shared `$errors` Blade template variable. Or, in the case of an XHR request, the validation errors will be returned with a 422 HTTP response.

### Two Factor Authentication

Most Scorch features can be customized via action classes. However, for security, Scorch's two-factor authentication services are encapsulated within Scorch and should not require customization.

The two-factor authentication actions lack dedicated views and should be included on the `user profile` view.

To enable two-factor authentication a user is required to send a `POST` request to `/two-factor-authentication`. Password should be confirmed before sending the request to enable two-factor authentication. Password confirmation should be done through a seperate end point and not by including a `password` parameter to the enable two-factor-authentication request.

To disable two-factor-authentication a `DELETE` request must be sent to `/two-factor-authentication`. This also requires password to be confirmed but can be bypassed if password was confirmed previously.

### API Token Authentication

> API tokens are mainly used to authenticate third-party application making a request to your API from a different domain. Your own first-party SPA should use Scorch's built-in [SPA authentication features](#spa-authentication).

#### Issuing API Tokens

Scorch allows you to issue API tokens / personal access tokens that may be used to authenticate API requests to your application. When making requests using API tokens, the token should be included in the `Authorization` header as a `Bearer` token.

To begin issuing tokens for users, your User model should use the `Emberfuse\Scorch\Models\Traits\HasApiTokens` trait:

```php
use c\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

To issue a token, you may use the `createToken` method. The `createToken` method returns a `Emberfuse\Scorch\Actions\CreateAccessToken` instance. API tokens are hashed using `SHA-256` hashing before being stored in your database, but you may access the plain-text value of the token using the `plainTextToken` property of the `CreateAccessToken` instance. You should display this value to the user immediately after the token has been created:

```php
use Illuminate\Http\Request;

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
```

You may access all of the user's `tokens` using the tokens Eloquent relationship provided by the `HasApiTokens` trait:

```php
foreach ($user->tokens as $token) {
    //
}
```

#### Token Abilities

Scorch allows you to assign "abilities" to tokens. Abilities serve a similar purpose as OAuth's "scopes". You may pass an array of string abilities as the second argument to the `createToken` method:

```php
return $user->createToken('token-name', ['server:update'])->plainTextToken;
```

When handling an incoming request authenticated by Scorch, you may determine if the token has a given ability using the `tokenCan` method:

```php
if ($user->tokenCan('server:update')) {
    //
}
```

##### First-Party UI Initiated Requests

For convenience, the `tokenCan` method will always return true if the incoming authenticated request was from your first-party SPA and you are using scorch's built-in **SPA authentication**.

However, this does not necessarily mean that your application has to allow the user to perform the action. Typically, your application's **authorization policies** will determine if the token has been granted the permission to perform the abilities as well as check that the user instance itself should be allowed to perform the action.

For example, if we imagine an application that manages servers, this might mean checking that token is authorized to update servers and that the server belongs to the user:

```php
return $request->user()->id === $server->user_id &&
    $request->user()->tokenCan('server:update')
```

At first, allowing the `tokenCan` method to be called and always return `true` for first-party UI initiated requests may seem strange; however, it is convenient to be able to always assume an API token is available and can be inspected via the `tokenCan` method. By taking this approach, you may always call the `tokenCan` method within your application's authorizations policies without worrying about whether the request was triggered from your application's UI or was initiated by one of your API's third-party consumers.

#### Protecting Routes

To protect routes so that all incoming requests must be authenticated, you should attach the **scorch** authentication guard to your protected routes within your `routes/web.php` and `routes/api.php` route files. This guard will ensure that incoming requests are authenticated as either stateful, cookie authenticated requests or contain a valid API token header if the request is from a third party.

You may be wondering why we suggest that you authenticate the routes within your application's `routes/web.php` file using the scorch guard. Remember, scorch will first attempt to authenticate incoming requests using Laravel's typical session authentication cookie. If that cookie is not present then scorch will attempt to authenticate the request using a token in the request's Authorization header. In addition, authenticating all requests using scorch ensures that we may always call the `tokenCan` method on the currently authenticated user instance:

```php
use Illuminate\Http\Request;

Route::middleware('auth:scorch')->get('/user', function (Request $request) {
    return $request->user();
});
```

#### Revoking Tokens

You may "revoke" tokens by deleting them from your database using the tokens relationship that is provided by the `Emberfuse\Scorch\Models\Traits\HasApiTokens` trait:

```php
// Revoke all tokens...
$user->tokens()->delete();

// Revoke the token that was used to authenticate the current request...
$request->user()->currentAccessToken()->delete();

// Revoke a specific token...
$user->tokens()->where('id', $tokenId)->delete();
```

### SPA Authentication

Scorch also exists to provide a simple method of authenticating single page applications (SPAs) that need to communicate with a Laravel powered API. These SPAs might exist in the same repository as your Laravel application or might be an entirely separate repository.

For this feature, Scorch does not use tokens of any kind. Instead, Scorch uses Laravel's built-in cookie based session authentication services. This approach to authentication provides the benefits of CSRF protection, session authentication, as well as protects against leakage of the authentication credentials via XSS.

> In order to authenticate, your SPA and API must share the same top-level domain. However, they may be placed on different subdomains.

#### Configuration

##### Configuring Your First-Party Domains

First, you should configure which domains your SPA will be making requests from. You may configure these domains using the `stateful` configuration option in your scorch configuration file. This configuration setting determines which domains will maintain "stateful" authentication using Laravel session cookies when making requests to your API.

> If you are accessing your application via a URL that includes a port (127.0.0.1:8000), you should ensure that you include the port number with the domain.

##### Scorch Middleware

Next, you should add Scorch's middleware to your `api` middleware group within your `app/Http/Kernel.php` file. This middleware is responsible for ensuring that incoming requests from your SPA can authenticate using Laravel's session cookies, while still allowing requests from third parties or mobile applications to authenticate using API tokens:

```php
'api' => [
    \Emberfuse\Scorch\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

##### CORS & Cookies

If you are having trouble authenticating with your application from an SPA that executes on a separate subdomain, you have likely misconfigured your CORS (Cross-Origin Resource Sharing) or session cookie settings.

You should ensure that your application's CORS configuration is returning the `Access-Control-Allow-Credentials` header with a value of `True`. This may be accomplished by setting the `supports_credentials` option within your application's `config/cors.php` configuration file to true (cors.php found only in Laravel applications).

In addition, you should enable the `withCredentials` option on your application's global `axios` instance. Typically, this should be performed in your `resources/js/bootstrap.js` file. If you are not using Axios to make HTTP requests from your frontend, you should perform the equivalent configuration on your own HTTP client:

```javascript
axios.defaults.withCredentials = true;
```

Finally, you should ensure your application's session cookie domain configuration supports any subdomain of your root domain. You may accomplish this by prefixing the domain with a leading `.` within your application's `config/session.php` configuration file:

```php
'domain' => '.domain.com',
```

#### Authenticating

##### CSRF Protection

To authenticate your SPA, your SPA's "login" page should first make a request to the `/csrf-cookie` endpoint to initialize CSRF protection for the application:

```javascript
axios.get('/csrf-cookie').then(response => {
    // Login...
});
```

During this request, Scorch will set an `XSRF-TOKEN` cookie containing the current CSRF token. This token should then be passed in an `X-XSRF-TOKEN` header on subsequent requests, which some HTTP client libraries like Axios and the Angular HttpClient will do automatically for you. If your JavaScript HTTP library does not set the value for you, you will need to manually set the `X-XSRF-TOKEN` header to match the value of the `XSRF-TOKEN` cookie that is set by this route.

##### Logging In

Once CSRF protection has been initialized, you should make a `POST` request to your Laravel application's `/login` route.

If the login request is successful, you will be authenticated and subsequent requests to your application's routes will automatically be authenticated via the session cookie that the Laravel application issued to your client. In addition, since your application already made a request to the `/csrf-cookie` route, subsequent requests should automatically receive CSRF protection as long as your JavaScript HTTP client sends the value of the `XSRF-TOKEN` cookie in the `X-XSRF-TOKEN` header.

Of course, if your user's session expires due to lack of activity, subsequent requests to the Laravel application may receive `401` or `419` HTTP error response. In this case, you should redirect the user to your SPA's login page.

#### Protecting Routes

To protect routes so that all incoming requests must be authenticated, you should attach the `scorch` authentication guard to your API routes within your `routes/api.php` file. This guard will ensure that incoming requests are authenticated as either a stateful authenticated requests from your SPA or contain a valid API token header if the request is from a third party:

```php
use Illuminate\Http\Request;

Route::middleware('auth:scorch')->get('/user', function (Request $request) {
    return $request->user();
});
```

### Mobile Application Authentication

You may also use Scorch tokens to authenticate your mobile application's requests to your API. The process for authenticating mobile application requests is similar to authenticating third-party API requests; however, there are small differences in how you will issue the API tokens.

#### Issuing API Tokens

To get started, create a route that accepts the user's email / username, password, and device name, then exchanges those credentials for a new Scorch token. The "device name" given to this endpoint is for informational purposes and may be any value you wish. In general, the device name value should be a name the user would recognize, such as "John's Nokia".

Typically, you will make a request to the token endpoint from your mobile application's "login" screen. The endpoint will return the plain-text API token which may then be stored on the mobile device and used to make additional API requests:

```php
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Route::post('/create/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});
```

When the mobile application uses the token to make an API request to your application, it should pass the token in the `Authorization` header as a `Bearer` token.

> When issuing tokens for a mobile application, you can also specify token abilities.

#### Protecting Routes

As previously documented, you may protect routes so that all incoming requests must be authenticated by attaching the scorch authentication guard to the routes:

```php
Route::middleware('auth:scorch')->get('/user', function (Request $request) {
    return $request->user();
});
```

#### Revoking Tokens

To allow users to revoke API tokens issued to mobile devices, you may list them by name, along with a "Revoke" button, within an "account settings" portion of your web application's UI. When the user clicks the "Revoke" button, you can delete the token from the database. Remember, you can access a user's API tokens via the tokens relationship provided by the `Emberfuse\Scorch\Models\Traits\HasApiTokens` trait:

```php
// Revoke all tokens...
$user->tokens()->delete();

// Revoke a specific token...
$user->tokens()->where('id', $tokenId)->delete();
```

## Contributing

Thank you for considering contributing to Scorch! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Emberfuse community is welcoming to all, please review and abide by the [Code of Conduct](.github/CODE_OF_CONDUCT.md).

## Security Vulnerabilities

Please review [our security policy](https://github.com/emberfuse/scorch/security/policy) on how to report security vulnerabilities.

## License

Emberfuse Scorch is open-sourced software licensed under the [MIT license](LICENSE.md).
