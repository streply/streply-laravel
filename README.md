## Streply SDK for Laravel framework

### Install

Install the `streply/streply-laravel` package:

```bash
composer require streply/streply-laravel
```

### Add the service provider to config/app.php

This step is needed only for Laravel versions below 11.X.

```php {filename:config.app.php}
Streply\Laravel\ServiceProvider::class,
```

## Enable capturing exception

### Laravel 11.X

Enable capturing exception in bootstrap/app.php:

```php {filename:bootstrap/app.php}
<?php

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

```php {filename:App/Exceptions/Handler.php}
<?php

public function register()
{
    $this->reportable(function (Throwable $e) {
        try {
            \Streply\Exception($e);
        } catch(\Exception $e) {}
    });
}
```
### Configure

Configure the Streply DSN with this command:

```shell
php artisan streply:publish https://clientPublicKey@api.streply.com/projectId
```
