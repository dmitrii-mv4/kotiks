<?php

namespace App\Http;

use App\Providers\ModuleMiddlewareServiceProvider;

class ModuleMiddleware
{
    /**
     * Load module middlewares dynamically
     */
    public function loadModuleMiddlewares(): array
    {
        return ModuleMiddlewareServiceProvider::loadModuleMiddlewares();
    }
}