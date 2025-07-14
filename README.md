# Streply SDK for Laravel framework

## Requirements

- **PHP**: 8.2, 8.3, or 8.4
- **Laravel**: 10.x, 11.x, or 12.x

## Install

Install the `streply/streply-laravel` package:

```bash
composer require streply/streply-laravel
```

## Add the service provider to config/app.php

This step is needed only for Laravel versions below 11.x.

```php
// config/app.php
Streply\Laravel\ServiceProvider::class,
```

## Enable capturing exception

### Laravel 12.x

Enable capturing exception in bootstrap/app.php:

```php
<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(static function (Throwable $exception) {
            \Streply\Exception($exception);
        });
    })->create();
```

### Laravel 11.x

Enable capturing exception in bootstrap/app.php:

```php
<?php
// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(static function (Throwable $exception) {
            \Streply\Exception($exception);
        });
    })->create();
```

### Laravel 8.x, 9.x and 10.x

Enable capturing exception in App/Exceptions/Handler.php:

```php
<?php
// App/Exceptions/Handler.php

public function register()
{
    $this->reportable(function (Throwable $e) {
        try {
            \Streply\Exception($e);
        } catch(\Exception $e) {}
    });
}
```

## Configure

Configure the Streply DSN with this command:

```bash
php artisan streply:publish https://clientPublicKey@api.streply.com/projectId
```

Or manually add to your `.env` file:

```env
STREPLY_DSN=https://clientPublicKey@api.streply.com/projectId
```

## PHP 8.4 Compatibility

This package is fully compatible with PHP 8.4 and utilizes modern PHP features for better performance:

- **Strict types** for improved performance and type safety
- **Enhanced error handling** with better type checking
- **Optimized for JIT compilation** in PHP 8.4

## Laravel 12 Support

This package supports Laravel 12.x with all new features:

- **New application structure** with bootstrap/app.php
- **Modern exception handling** configuration
- **Full compatibility** with Laravel 12 starter kits