<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Modules\Auth\Repositories\UserRepository;
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
        return [
            UserRepositoryInterface::class => UserRepository::class,
        ];
    }
}
