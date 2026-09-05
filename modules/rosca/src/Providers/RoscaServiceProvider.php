<?php

namespace Modules\Rosca\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Modules\Rosca\Console\CloseRoundsCommand;
use Modules\Rosca\Listeners\SendWinnerNotification;
use Modules\Rosca\Events\WinnerSelected;

class RoscaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Routes
        if (file_exists(__DIR__ . '/../../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/api.php');
        }

        if (file_exists(__DIR__ . '/../../Routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        }

        // Views & translations
        $this->loadViewsFrom(__DIR__ . '/../../Resources/views', 'rosca');
        $this->loadTranslationsFrom(__DIR__ . '/../../Resources/lang', 'rosca');

        // Publish assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../Database/Migrations/' => database_path('migrations')
            ], 'rosca-migrations');

            $this->publishes([
                __DIR__ . '/../../Resources/views' => resource_path('views/vendor/rosca')
            ], 'rosca-views');

            $this->publishes([
                __DIR__ . '/../../Config/config.php' => config_path('rosca.php')
            ], 'rosca-config');

            // Register commands
            $this->commands([
                CloseRoundsCommand::class,
            ]);
        }

        // Event listeners
        Event::listen(WinnerSelected::class, [SendWinnerNotification::class, 'handle']);

        // Schedule close rounds according to config
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $cron = config('rosca.close_rounds_schedule', '0 0 * * *');

            // If cron is empty, do not schedule
            if (! empty($cron)) {
                // schedule as daily at midnight by default — allow overriding via a cron expression
                // If user set cron expression, we try to use ->cron()
                try {
                    $schedule->command('rosca:close-rounds')->cron($cron);
                } catch (\Throwable $e) {
                    // fallback to daily
                    $schedule->command('rosca:close-rounds')->daily();
                }
            }
        });
    }

    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/../../Config/config.php', 'rosca');

        // Bind any additional services here if needed
    }
}
