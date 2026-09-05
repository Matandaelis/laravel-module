<?php

namespace Modules\Rosca\Providers;

use Illuminate\Support\ServiceProvider;

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
        }
    }

    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/../../Config/config.php', 'rosca');

        // Bind any additional services here if needed
    }
}
