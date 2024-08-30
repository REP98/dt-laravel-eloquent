<?php
namespace DTLaravelEloquent;

use Illuminate\Support\ServiceProvider;
use DTLaravelEloquent\Console\RDTInstallCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class RDataTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Cargar rutas
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Cargar vistas
        $this->loadViewsFrom(__DIR__.'/resources/views', 'datatable');
        Blade::anonymousComponentPath(__DIR__.'/resources/views/components/DT', 'dt');

        // Publicar configuración
        $this->publishes([
            __DIR__.'/../config/RDataTable.php' => config_path('RDataTable.php'),
        ]);

        // Cargar archivos de idioma
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'datatable');

        // Publicar archivos CSS y JS
        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/dtlaravel'),
        ], 'public');
    }

    public function register()
    {
        // Registrar configuración
        $this->mergeConfigFrom(
            __DIR__.'/../config/RDataTable.php', 'RDataTable'
        );

        // Registrar comandos
        if ($this->app->runningInConsole()) {
            $this->commands([
                RDTInstallCommand::class,
            ]);
        }
    }

     /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path.'*_'.$migrationFileName))
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
