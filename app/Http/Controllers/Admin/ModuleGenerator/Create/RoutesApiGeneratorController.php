<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RoutesApiGeneratorController extends CreateModuleController
{
    public function createRoutesAPI($validated)
    {
        $nameModuleCode = $validated['code'];
        $routesApiPath = base_path('routes/api/modules/');

        // Create the directory if it doesn't exist
        if (!File::isDirectory($routesApiPath)) {
            File::makeDirectory($routesApiPath, 0755, true);
        }
        
        $routeFilePath = $routesApiPath . $nameModuleCode . '.php';
        
        // Define the content of the new route file
        $controllerName = Str::studly($nameModuleCode) . 'Controller';
        
        $content = "<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\\Modules\\$controllerName;

Route::apiResource('" . Str::lower($nameModuleCode) . "', $controllerName::class);
";
    
        // Create the file and write the content
        if (File::put($routeFilePath, $content) === false) {
            throw new \Exception("Failed to create route file:" . $routeFilePath);
        }
        
        // Note: You must still manually register this file in bootstrap/app.php
    }
}