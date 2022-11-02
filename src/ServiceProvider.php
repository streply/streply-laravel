<?php

namespace Streply\StreplyLaravel;

use Streply\StreplyLaravel\Console\PublishCommand;
use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Streply\Exceptions\InvalidDsnException;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @throws InvalidDsnException
     */
    public function boot()
    {
        if (null !== config('streply-laravel.dsn')) {
            $streplyClient = new StreplyClient(config('streply-laravel.dsn'), ['environment' => config('app.env')]);

            $streplyClient->initialize();
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
