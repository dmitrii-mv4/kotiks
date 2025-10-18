<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MiddlewareGeneratorController extends CreateModuleController
{
    // $modelName - Str::studly($validated['code'] . 'Module'); NewsModule

    /**
     * Главная функция отвечающая за создание всех файлов Middleware
     */
    public function createMiddleware($validated, $modelName)
    {
        // Обзначение повторяющих данных
        $moduleName = Str::studly($validated['code']);

        // Создаем папку Modules если нужно
        $modulesMiddlewarePath = $this->ensureMiddlewareDir($moduleName);

        // Создаём index, create, update, delete файлы
        $this->createIndexMiddleware($moduleName, $modelName);
        $this->createCreateMiddleware($moduleName, $modelName);
        $this->createUpdateMiddleware($moduleName, $modelName);
        $this->createDeleteMiddleware($moduleName, $modelName);

        // Обновляем файл app.php
        $this->updateAppMiddleware($validated, $modelName, $moduleName);
    }

    /**
     * Создание директории Modules для Middleware
     */
    private function ensureMiddlewareDir($moduleName)
    {
        $modulesMiddlewarePath = app_path('Http/Middleware/Modules/'.$moduleName);
        if (!File::exists($modulesMiddlewarePath)) {
            File::makeDirectory($modulesMiddlewarePath, 0755, true);
        }
        return $modulesMiddlewarePath;
    }

    /**
     * Создаём Middleware для просмотра всех записей
     */
    public function createIndexMiddleware($moduleName, $modelName)
    {
        $className = $moduleName.'IndexMiddleware';

        // Создаем файл Middleware в правильной папке
        $middlewarePath = app_path("Http/Middleware/Modules/".$moduleName."/".$moduleName."IndexMiddleware.php");

        $content = <<<PHP
<?php

namespace App\Http\Middleware\Modules\\$moduleName;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Modules\\$modelName;

class $className
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  \$next
     */
    public function handle(Request \$request, Closure \$next): Response
    {
        // Проверяем разрешение через Gate
        if (Gate::allows('viewAny', $modelName::class)) {
            return \$next(\$request);
        }
        
        // Если доступ запрещен
        abort(403, 'Доступ запрещен');
    }
}
PHP;

        File::put($middlewarePath, $content);
        
        if (!File::exists($middlewarePath)) {
            throw new \Exception("Файл Middleware не найден: ".$middlewarePath);
        }

        return "Middleware создан: " . $middlewarePath;
    }

    /**
     * Создаём Middleware для создания записей
     */
    public function createCreateMiddleware($moduleName, $modelName)
    {
        $className = $moduleName.'CreateMiddleware';

        // Создаем файл Middleware в правильной папке
        $middlewarePath = app_path("Http/Middleware/Modules/".$moduleName."/".$moduleName."CreateMiddleware.php");

        $content = <<<PHP
<?php

namespace App\Http\Middleware\Modules\\$moduleName;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Modules\\$modelName;

class $className
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  \$next
     */
    public function handle(Request \$request, Closure \$next): Response
    {
        // Проверяем разрешение через Gate
        if (Gate::allows('create', $modelName::class)) {
            return \$next(\$request);
        }
        
        // Если доступ запрещен
        abort(403, 'Доступ запрещен');
    }
}
PHP;

        File::put($middlewarePath, $content);
        
        if (!File::exists($middlewarePath)) {
            throw new \Exception("Файл Middleware не найден: ".$middlewarePath);
        }
        
        return "Middleware создан: " . $middlewarePath;
    }

    /**
     * Создаём Middleware для редактирования записей
     */
    public function createUpdateMiddleware($moduleName, $modelName)
    {
        $className = $moduleName.'UpdateMiddleware';

        // Создаем файл Middleware в правильной папке
        $middlewarePath = app_path("Http/Middleware/Modules/".$moduleName."/".$moduleName."UpdateMiddleware.php");

        $content = <<<PHP
<?php

namespace App\Http\Middleware\Modules\\$moduleName;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Modules\\$modelName;

class $className
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  \$next
     */
    public function handle(Request \$request, Closure \$next): Response
    {
        // Проверяем разрешение через Gate
        if (Gate::allows('update', $modelName::class)) {
            return \$next(\$request);
        }
        
        // Если доступ запрещен
        abort(403, 'Доступ запрещен');
    }
}
PHP;

        File::put($middlewarePath, $content);
        
        if (!File::exists($middlewarePath)) {
            throw new \Exception("Файл Middleware не найден: ".$middlewarePath);
        }
        
        return "Middleware создан: " . $middlewarePath;
    }

    /**
     * Создаём Middleware для удаление записей
     */
    public function createDeleteMiddleware($moduleName, $modelName)
    {
        $className = $moduleName.'DeleteMiddleware';

        // Создаем файл Middleware в правильной папке
        $middlewarePath = app_path("Http/Middleware/Modules/".$moduleName."/".$moduleName."DeleteMiddleware.php");

        $content = <<<PHP
<?php

namespace App\Http\Middleware\Modules\\$moduleName;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Modules\\$modelName;

class $className
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  \$next
     */
    public function handle(Request \$request, Closure \$next): Response
    {
        // Проверяем разрешение через Gate
        if (Gate::allows('delete', $modelName::class)) {
            return \$next(\$request);
        }
        
        // Если доступ запрещен
        abort(403, 'Доступ запрещен');
    }
}
PHP;

        File::put($middlewarePath, $content);
        
        if (!File::exists($middlewarePath)) {
            throw new \Exception("Файл Middleware не найден: ".$middlewarePath);
        }
        
        return "Middleware создан: " . $middlewarePath;
    }

    /**
     * Обновляем файл app.php
     */
    public function updateAppMiddleware($validated, $modelName, $moduleName)
    {
        $filePath = base_path('bootstrap/app.php');
        $content = file_get_contents($filePath);
        
        // Определяем типы middleware для обработки
        $middlewareTypes = ['Index', 'Create', 'Update', 'Delete'];
        
        // 1. Добавляем use statements в секцию "Use Modules"
        $useModuleComment = "// Use Modules";
        $useStatements = "";
        
        foreach ($middlewareTypes as $type) {
            $middlewareClass = $moduleName . $type . "Middleware";
            $fullNamespace = "App\Http\Middleware\Modules\\" . $moduleName . "\\" . $middlewareClass;
            $useStatement = "use " . $fullNamespace . ";";
            
            // Проверяем, есть ли уже этот use statement
            if (strpos($content, $useStatement) === false) {
                $useStatements .= "\n" . $useStatement;
            }
        }
        
        // Добавляем все отсутствующие use statements
        if (!empty(trim($useStatements))) {
            $content = str_replace(
                $useModuleComment,
                $useModuleComment . $useStatements,
                $content
            );
        }
        
        // 2. Добавляем aliases ПОСЛЕ комментария "// Modules"
        $modulesComment = "            // Modules";
        $aliasStatements = "";
        
        foreach ($middlewareTypes as $type) {
            $middlewareClass = $moduleName . $type . "Middleware";
            $aliasName = $validated['code'] . '_' . strtolower($type);
            $aliasStatement = "            '" . $aliasName . "' => " . $middlewareClass . "::class,";
            
            // Проверяем, есть ли уже этот alias
            if (strpos($content, "'" . $aliasName . "' =>") === false) {
                $aliasStatements .= "\n" . $aliasStatement;
            } else {
                // Если alias уже существует, обновляем его
                $pattern = "/'" . preg_quote($aliasName, '/') . "'\\s*=>\\s*[^,]+/";
                $replacement = "'" . $aliasName . "' => " . $middlewareClass . "::class";
                $content = preg_replace($pattern, $replacement, $content);
            }
        }
        
        // Добавляем все отсутствующие aliases
        if (!empty(trim($aliasStatements))) {
            $content = str_replace(
                $modulesComment,
                $modulesComment . $aliasStatements,
                $content
            );
        }
        
        // Записываем обновленный content обратно в файл
        file_put_contents($filePath, $content);
    }
}