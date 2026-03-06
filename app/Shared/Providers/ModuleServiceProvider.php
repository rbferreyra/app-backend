<?php

namespace App\Shared\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Return the module's route file path.
     * e.g. __DIR__ . '/../Routes/api.php'
     */
    abstract protected function routeFilePath(): string;

    /**
     * Return the route prefix for this module.
     * e.g. 'auth', 'properties'
     */
    abstract protected function routePrefix(): string;

    /**
     * Bind repositories to their interfaces.
     * Override in each module provider.
     */
    protected function bindings(): array
    {
        return [];
    }

    public function register(): void
    {
        foreach ($this->bindings() as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    public function boot(): void
    {
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routeFile = $this->routeFilePath();

        if (! file_exists($routeFile)) {
            return;
        }

        Route::prefix('api/' . $this->routePrefix())
            ->middleware('api')
            ->group($routeFile);
    }
}
