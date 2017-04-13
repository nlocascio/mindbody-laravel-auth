# Laravel MINDBODY Auth
This package is a Laravel User Provider which authenticates user logins through MINDBODY. It exposes two auth drivers: one for authenticating Client credentials, and one for authenticating Staff credentials.

## Requirements
This package requires:
- PHP __7.0__+
- Laravel __5.1__+
- Nlocascio/Mindbody-Laravel __0.2.0__+

## Installation
Install the package through Composer:

```shell
composer require nlocascio/mindbody-laravel-auth
```

__This package requires `nlocascio/mindbody-laravel` to communicate with the MINDBODY API. You must [configure that package first](https://github.com/nlocascio/mindbody-laravel) before proceeding.__

### Laravel
#### Register the Service Provider
In `config/app.php`, append to the `providers` key **before** `App\Providers\AuthServiceProvider::class` is declared:

```php
Nlocascio\MindbodyAuth\Providers\MindbodyAuthServiceProvider::class
```

#### Configure the User Provider

In your app's `config/auth.php`, add the following to the `providers` key:

##### For authenticating Clients with MINDBODY:
```php
'mindbody_clients' => [
    'driver' => 'mindbody_client',
    'model' => App\User::class
],
```

##### For authenticating Staff with MINDBODY:
```php
'mindbody_staff' => [
    'driver' => 'mindbody_staff',
    'model' => App\User::class
]
```

Note that your `model` can point to any Eloquent model which implements `Illuminate\Contracts\Auth\Authenticatable`. Depending on the needs of your application, you may prefer to have different models for different types of users; however, using the default `App/User.php` will work for many cases.
  
#### Configure the Authentication Guards
 
In your app's `config/auth.php`, add the following to the `guards` key:


##### For MINDBODY Client credentials:
```php
'mindbody_client' => [
    'driver' => 'session',
    'provider' => 'mindbody_client'
],
```

##### or for MINDBODY Staff credentials:
```php
'mindbody_staff' => [
    'driver' => 'session',
    'provider' => 'mindbody_staff'
]
```

#### Use the Guards in your Middleware
Now that you've registered and configured the guards, you may use them in your application by using the `auth:mindbody_client` or `auth:mindbody_staff` middleware.

You can set one of these guards to be the default authentication guard in `config/auth.php` under the `defaults` key:

```php
'defaults' => [
    'guard'     => 'mindbody_client',      // or 'mindbody_staff'
    'passwords' => 'users',
],
```

