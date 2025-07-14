<?php

declare(strict_types=1);

namespace Streply\Laravel;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Streply\Exceptions\InvalidDsnException;
use Streply\Laravel\Console\PublishCommand;
use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Streply\Enum\EventFlag;

class ServiceProvider extends BaseServiceProvider
{
    private bool $isInitialized = false;

    public function boot(): void
    {
        $this->initializeStreply();

        if ($this->app->runningInConsole()) {
            $this->registerConsoleFeatures();
        }
    }

    private function initializeStreply(): void
    {
        $dsn = config('streply-laravel.dsn');

        if (empty($dsn)) {
            return;
        }

        $client = new StreplyClient($dsn, [
            'environment' => config('app.env'),
        ]);

        $client->initialize();
        $client->user();

        $this->isInitialized = true;
    }

    private function registerConsoleFeatures(): void
    {
        if ($this->app instanceof Laravel) {
            $this->publishes([
                __DIR__ . '/../config/streply-laravel.php' => config_path('streply-laravel.php'),
            ], 'config');

            $this->listenArtisanCommands();
        }

        $this->registerArtisanCommands();
    }

    private function registerArtisanCommands(): void
    {
        $this->commands([
            PublishCommand::class,
        ]);
    }

    private function listenArtisanCommands(): void
    {
        Event::listen(CommandFinished::class, function (CommandFinished $event): void {
            if (!$this->isInitialized || !is_string($event->command)) {
                return;
            }

            $command = $event->command;
            $arguments = $event->input->getArguments();

            \Streply\withScope(function (\Streply\Scope $scope) use ($command, $arguments): void {
                $scope->setFlag(EventFlag::COMMAND);
                \Streply\Activity($command, $arguments);
            });
        });
    }
}