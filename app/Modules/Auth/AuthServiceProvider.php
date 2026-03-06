<?php

namespace App\Modules\Auth;

use App\Shared\Providers\ModuleServiceProvider;

class AuthServiceProvider extends ModuleServiceProvider
{
    protected function routeFilePath(): string
    {
        return __DIR__ . '/Routes/api.php';
    }

    protected function routePrefix(): string
    {
        return 'auth';
    }

    protected function bindings(): array
    {
        // Will be populated as repositories are created
        // e.g. UserRepositoryInterface::class => UserRepository::class
        return [];
    }
}
