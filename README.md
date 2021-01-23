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
