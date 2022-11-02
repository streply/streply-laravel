## Streply SDK for Laravel framework

### Install

Install the `streply/streply-laravel` package:

```bash
composer require streply/streply-laravel
```

### Add the service provider to config/app.php

```php {filename:config.app.php}
Streply\StreplyLaravel\ServiceProvider::class,
```

### Enable capturing exception in App/Exceptions/Handler.php:
```php {filename:App/Exceptions/Handler.php}
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
php artisan streply-laravel:publish https://clientPublicKey@api.streply.com/projectId
```
