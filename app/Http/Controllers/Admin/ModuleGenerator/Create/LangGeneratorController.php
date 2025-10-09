<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class LangGeneratorController extends CreateModuleController
{
    public function createLangs($validated)
    {
        $moduleName = $validated['code'];
        $codeProperties = (array) $validated['code_property'];
        $nameProperties = (array) $validated['name_property'];
        
        $translations = [];
        
        foreach ($codeProperties as $index => $codeProperty) {
            $nameProperty = $nameProperties[$index] ?? $codeProperty;
            // Экранируем специальные символы
            $translations[$codeProperty] = addslashes($nameProperty);
        }
        
        $ruPath = resource_path("lang/ru/$moduleName.php");
        $enPath = resource_path("lang/en/$moduleName.php");
        
        File::ensureDirectoryExists(dirname($ruPath));
        File::ensureDirectoryExists(dirname($enPath));
        
        // Генерируем содержимое с использованием var_export для безопасности
        $fileContent = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
        
        File::put($ruPath, $fileContent);
        File::put($enPath, $fileContent);
        
        return true;
    }
}