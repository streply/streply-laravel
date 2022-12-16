<?php

namespace Streply\StreplyLaravel;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\StreplyLaravel\Console\PublishCommand;
use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Streply\Store\Providers\MemoryProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @throws InvalidDsnException
     * @throws InvalidUserException
     */
    public function boot(): void
    {
        if (null !== config('streply-laravel.dsn')) {
            $streplyClient = new StreplyClient(config('streply-laravel.dsn'), [
				'environment' => config('app.env'),
				'storeProvider' => new MemoryProvider(),
			]);

            $streplyClient->initialize();

            $this->app->terminating(function () use ($streplyClient) {
				$streplyClient->user();
				$streplyClient->flush();
            });
        }

        if ($this->app->runningInConsole()) {
            if ($this->app instanceof Laravel) {
                $this->publishes([
                    __DIR__ . '/../config/streply-laravel.php' => config_path('streply-laravel.php'),
                ], 'config');
            }

            $this->registerArtisanCommands();
        }
    }

    /**
     * @return void
     */
    protected function registerArtisanCommands(): void
    {
        $this->commands([
            PublishCommand::class,
        ]);
    }
}
