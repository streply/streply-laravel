<?php

namespace Streply\StreplyLaravel;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Streply\Exceptions\InvalidDsnException;
use Streply\StreplyLaravel\Console\PublishCommand;
use Illuminate\Foundation\Application as Laravel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Streply\Store\Providers\MemoryProvider;
use Streply\Enum\EventFlag;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * @var StreplyClient
     */
    private StreplyClient $streplyClient;

    /**
     * @var bool
     */
    private bool $isInitialized = false;

    /**
     * @throws InvalidDsnException
     */
    public function boot(): void
    {
        if(null !== config('streply-laravel.dsn')) {
            $this->streplyClient = new StreplyClient(config('streply-laravel.dsn'), [
				'environment' => config('app.env'),
				'storeProvider' => new MemoryProvider(),
			]);

            $this->streplyClient->initialize();
            $this->isInitialized = true;

            $this->app->terminating(function () {
				$this->streplyClient->user();

				if(isset(\Route::getCurrentRoute()->uri) && is_string(\Route::getCurrentRoute()->uri)) {
					$this->streplyClient->setRoute(\Route::getCurrentRoute()->uri);
				}

				$this->streplyClient->flush();
            });
        }

        if($this->app->runningInConsole()) {
            if($this->app instanceof Laravel) {
                $this->publishes([
                    __DIR__ . '/../config/streply-laravel.php' => config_path('streply-laravel.php'),
                ], 'config');

                $this->listenArtisanCommands();
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

    /**
     * @return void
     */
    protected function listenArtisanCommands(): void
    {
        Event::listen(CommandFinished::class, function (CommandFinished $event) {
            if($this->isInitialized && is_string($event->command)) {
                $this->streplyClient
					->activity(
						$event->command,
						$event->input->getArguments()
					)
					->flag(
						EventFlag::COMMAND
					)
				;
            }
        });
    }
}
