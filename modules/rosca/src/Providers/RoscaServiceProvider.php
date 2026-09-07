<?php

namespace Modules\Rosca\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Modules\Rosca\Console\CloseRoundsCommand;
use Modules\Rosca\Listeners\SendWinnerNotification;
use Modules\Rosca\Events\WinnerSelected;
use Modules\Rosca\Contracts\GatewayInterface;
use Modules\Rosca\Gateways\MPesaGateway;
use Modules\Rosca\Gateways\ManualGateway;
use Modules\Rosca\Models\Rosca;
use Modules\Rosca\Policies\RoscaPolicy;

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

            if (! empty($cron)) {
                try {
                    $schedule->command('rosca:close-rounds')->cron($cron);
                } catch (\Throwable $e) {
                    $schedule->command('rosca:close-rounds')->daily();
                }
            }
        });

        // Register policy
        Gate::policy(Rosca::class, RoscaPolicy::class);
    }

    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/../../Config/config.php', 'rosca');

        // Bind gateway based on config
        $this->app->bind(GatewayInterface::class, function ($app) {
            $gateway = config('rosca.gateway', 'mpesa');

            if ($gateway === 'mpesa') {
                return new MPesaGateway(config('rosca.mpesa', []));
            }

            return new ManualGateway();
        });
    }
}
