<?php

namespace App\Http\Controllers\Admin\ModuleGenerator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\Permission;

class DeleteModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function delete($moduleCode)
    {
        // $moduleCode - код модуля, пример: news

        // Формируем названия
        $moduleName = Str::studly($moduleCode); // Название модуля
        $prefixModuleCode = 'module_' . $moduleCode; // module_news
        $modelName = Str::studly($moduleCode . 'Module');

        // Вызываем методы на удаление
        $this->moduleDatabaseDelete($moduleCode);
        $this->moduleControllerDelete($moduleCode);
        $this->moduleControllerApiDelete($moduleCode);
        $this->moduleLangsDelete($moduleCode);
        $this->moduleViewDelete($moduleCode);
        $this->moduleRequestsDelete($moduleName);
        $this->moduleRouterApiDelete($moduleCode);
        $this->moduleMiddlewareDelete($moduleName);
        $this->modulePolicyDelete($moduleName);
        $this->modulePermissionDelete($prefixModuleCode);
        $this->removeAppMiddleware($modelName, $moduleCode);

        return redirect()->route('admin.modules')->with('success', 'Модуль удалён');
    }

    /**
     * Удаление миграций и моделей
     */
    public function moduleDatabaseDelete($moduleCode)
    {
        try {
            // Формируем имена таблиц для поиска
            $tableSuffix = $moduleCode . '__module';
            $tableMainSuffix = $moduleCode . '_main__module';
            
            // Получаем все таблицы в базе данных
            $tables = DB::select('SHOW TABLES');
            
            // Определяем название колонки с именами таблиц
            $databaseName = config('database.connections.mysql.database');
            $tablesColumn = 'Tables_in_' . $databaseName;
            
            $deletedTables = [];
            
            // Временно отключаем проверку внешних ключей для избежания ошибок
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($tables as $table) {
                $tableName = $table->$tablesColumn;
                
                // Объединенная проверка для обоих суффиксов
                if (str_ends_with($tableName, $tableSuffix) || str_ends_with($tableName, $tableMainSuffix)) {
                    // Удаляем таблицу
                    Schema::dropIfExists($tableName);
                    $deletedTables[] = $tableName;
                }
            }

            // Удаляем записи о миграциях модуля
            // Предполагается, что миграции модуля содержат его название в имени
            DB::table('migrations')
                ->where('migration', 'LIKE', "%{$moduleCode}%")
                ->delete();
            
            // Включаем проверку внешних ключей обратно
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            // === Удаление миграций
            $migrationPath = database_path('migrations/modules/');

            // Ищем файлы по паттернам
            $mainModulePattern = '*'.$moduleCode.'_main__module_table.php';
            $modulePattern = '*'.$moduleCode.'__module_table.php';

            // Находим файлы соответствующие паттернам
            $mainModuleFiles = File::glob($migrationPath . '/' . $mainModulePattern);
            $moduleFiles = File::glob($migrationPath . '/' . $modulePattern);

            $deletedMigrationFiles = [];

            // Удаляем найденные файлы
            foreach (array_merge($mainModuleFiles, $moduleFiles) as $file) {
                if (File::exists($file)) {
                    File::delete($file);
                    $deletedMigrationFiles[] = basename($file);
                }
            }
            // === end Удаление миграций

            // === Удаление моделей Eloquent ===
            $modelModule = Str::studly($moduleCode);

            $modelPaths = [
                app_path('Models/Modules/'), // Стандартный путь к моделям
                // app_path('Admin/Modules/'), // Раскомментируйте, если ваши модели в поддиректориях
            ];

            $modelFiles = [];
            $modelPatterns = [
                '*'.$modelModule.'MainModule.php',
                '*'.$modelModule.'Module.php',
            ];

            foreach ($modelPaths as $modelPath) {
                foreach ($modelPatterns as $pattern) {
                    $foundModels = File::glob($modelPath . $pattern);
                    $modelFiles = array_merge($modelFiles, $foundModels);
                }
            }

            $deletedModelFiles = [];
            foreach ($modelFiles as $modelFile) {
                if (File::exists($modelFile)) {
                    File::delete($modelFile);
                    $deletedModelFiles[] = basename($modelFile);
                }
            }
            // === end Удаление моделей ===

            // Проверяем, были ли удалены какие-либо таблицы в БД
            if (empty($deletedTables)) {
                return redirect()->route('admin.modules')->with('warning', 'Не найдено таблиц для удаления с указанными суффиксами.');
            }
            
            return redirect()->route('admin.modules')->with('success', 'Модуль и связанные таблицы удалены: ' . implode(', ', $deletedTables));

        } catch (\Exception $e) {
            // Гарантируем, что проверка внешних ключей будет включена даже при ошибке
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return redirect()->route('admin.modules')->with('error', 'Ошибка при удалении модуля: ' . $e->getMessage());
        }
    }

    /**
     * Удаление контроллера
     */
    public function moduleControllerDelete($moduleCode)
    {
        $controllerModule = Str::studly($moduleCode);

        $controllerPaths = [
            app_path('Http/Controllers/Admin/Modules/'),
        ];

        $controllerFiles = [];
        $controllerPatterns = [
            '*'.$controllerModule.'Controller.php',
        ];

        $deletedControllerFiles = [];

        foreach ($controllerPaths as $controllerPath)
        {
            if (!File::exists($controllerPath)) {
                continue;
            }
                
            foreach ($controllerPatterns as $pattern)
            {
                $foundControllers = File::glob($controllerPath . $pattern);
                $controllerFiles = array_merge($controllerFiles, $foundControllers);
            }
                
            // Рекурсивный поиск в поддиректориях
            $allFiles = File::allFiles($controllerPath);
            foreach ($allFiles as $file)
            {
                $filename = $file->getFilename();
                
                foreach ($controllerPatterns as $pattern)
                {
                    if (fnmatch($pattern, $filename))
                    {
                        $controllerFiles[] = $file->getPathname();
                    }
                }
            }
        }

        // Удаляем дубликаты
        $controllerFiles = array_unique($controllerFiles);

        // ★★ ВАЖНО: ДОБАВЛЯЕМ УДАЛЕНИЕ НАЙДЕННЫХ ФАЙЛОВ ★★
        foreach ($controllerFiles as $controllerFile)
        {
            if (File::exists($controllerFile))
            {
                File::delete($controllerFile);
                $deletedControllerFiles[] = basename($controllerFile);
            }
        }
    }

    /**
     * Удаление контроллера API
     */
    public function moduleControllerApiDelete($moduleCode)
    {
        $controllerModule = Str::studly($moduleCode);

        $controllerPaths = [
            app_path('Http/Controllers/Api/Modules/'),
        ];

        $controllerFiles = [];
        $controllerPatterns = [
            '*'.$controllerModule.'Controller.php',
        ];

        $deletedControllerFiles = [];

        foreach ($controllerPaths as $controllerPath)
        {
            if (!File::exists($controllerPath)) {
                continue;
            }
                
            foreach ($controllerPatterns as $pattern)
            {
                $foundControllers = File::glob($controllerPath . $pattern);
                $controllerFiles = array_merge($controllerFiles, $foundControllers);
            }
                
            // Рекурсивный поиск в поддиректориях
            $allFiles = File::allFiles($controllerPath);
            foreach ($allFiles as $file)
            {
                $filename = $file->getFilename();
                
                foreach ($controllerPatterns as $pattern)
                {
                    if (fnmatch($pattern, $filename))
                    {
                        $controllerFiles[] = $file->getPathname();
                    }
                }
            }
        }

        // Удаляем дубликаты
        $controllerFiles = array_unique($controllerFiles);

        // ★★ ВАЖНО: ДОБАВЛЯЕМ УДАЛЕНИЕ НАЙДЕННЫХ ФАЙЛОВ ★★
        foreach ($controllerFiles as $controllerFile)
        {
            if (File::exists($controllerFile))
            {
                File::delete($controllerFile);
                $deletedControllerFiles[] = basename($controllerFile);
            }
        }
    }

    /**
     * Удаление языковых файлов модуля
     */
    public function moduleLangsDelete($moduleCode)
    {
        $langPaths = [
            resource_path('lang/en/modules/' . $moduleCode . '.php'),
            resource_path('lang/ru/modules/' . $moduleCode . '.php'),
        ];

        $deletedLangFiles = [];

        foreach ($langPaths as $langFile)
        {
            if (File::exists($langFile))
            {
                File::delete($langFile);
                $deletedLangFiles[] = basename($langFile);
            }
        }
    }

    /**
     * Удаление шаблонов blade
     */
    public function moduleViewDelete($moduleCode)
    {
        $viewPath = resource_path('views/admin/modules/' . $moduleCode);

        $deletedViewDirs = [];
        if (File::exists($viewPath))
        {
            File::deleteDirectory($viewPath);
            $deletedViewDirs[] = $viewPath;
        }
    }

    /**
     * Удаление Requests папки модуля
     */
    public function moduleRequestsDelete($moduleName)
    {
        $requestsModulePath = app_path('Http/Requests/Modules/' . $moduleName);

        $deletedRequestsDirs = [];
        if (File::exists($requestsModulePath))
        {
            File::deleteDirectory($requestsModulePath);
            $deletedRequestsDirs[] = $requestsModulePath;
        }
    }

    /**
     * Удаление Router Api модуля
     */
    public function moduleRouterApiDelete($moduleCode)
    {
        // Формируем путь к файлу
        $routerApiFile = base_path('routes/api/modules/' . $moduleCode . '.php');

        // Проверяем существование файла ДЕЙСТВИТЕЛЬНО простым способом
        if (file_exists($routerApiFile))
        {
            // Пытаемся удалить
            if (unlink($routerApiFile))
            {
                $deletedRoutersApiFiles[] = basename($routerApiFile);
                // Не забудьте очистить кеш маршрутов после удаления
                Artisan::call('route:clear');
            } else {
                logger("Не удалось удалить файл: " . $routerApiFile);
            }

        } else {
            // Логируем, что файл не найден (для отладки)
            logger("Файл для удаления не найден: " . $routerApiFile);
        }
    }

    /**
     * Удаление Middleware
     */
    public function moduleMiddlewareDelete($moduleName)
    {
        $middlewareModulePath = app_path('Http/Middleware/Modules/' . $moduleName);

        $deletedMiddlewareDirs = [];
        
        if (File::exists($middlewareModulePath))
        {
            File::deleteDirectory($middlewareModulePath);
            $deletedMiddlewareDirs[] = $middlewareModulePath;
        }
    }

    public function modulePolicyDelete($moduleName)
    {
        // Формируем путь к файлу
        $policyFile = app_path('Policies/Modules/' . $moduleName . 'Policy.php');

        // Проверяем существование файла ДЕЙСТВИТЕЛЬНО простым способом
        if (file_exists($policyFile))
        {
            // Пытаемся удалить
            if (unlink($policyFile))
            {
                $deletedPolicyFiles[] = basename($policyFile);
            } else {
                logger("Не удалось удалить файл: " . $policyFile);
            }

        } else {
            // Логируем, что файл не найден (для отладки)
            logger("Файл для удаления не найден: " . $policyFile);
        }
    }

    /**
     * Удаление разрешений
     */
    public function modulePermissionDelete($prefixModuleCode)
    {
        // Поиск через метод модели
        $permissionsToDelete = Permission::findByCodeModule($prefixModuleCode);

        // Удаление в контроллере
        foreach ($permissionsToDelete as $permission)
        {
            $permission->delete();
        }
    }

    /**
     * Удаление строчек модуля в app.php
     */
    public function removeAppMiddleware($modelName, $moduleCode)
    {
        $filePath = base_path('bootstrap/app.php');
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        
        if ($lines === false) {
            return false;
        }
        
        $middlewareTypes = ['Index', 'Create', 'Update', 'Delete'];
        $usePatterns = [];
        $aliasPatterns = [];
        
        // Подготавливаем паттерны для поиска
        foreach ($middlewareTypes as $type) {
            $middlewareClass = $modelName . $type . "Middleware";
            $usePatterns[] = "use App\Http\Middleware\Modules\\" . $modelName . "\\" . $middlewareClass . ";";
            $aliasPatterns[] = "'" . $moduleCode . '_' . strtolower($type) . "'";
        }
        
        // Фильтруем строки только между комментариями
        $filteredLines = [];
        $inUseSection = false;
        $inAliasSection = false;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Определяем начало и конец секций
            if ($trimmedLine === '// Use Modules') {
                $inUseSection = true;
                $filteredLines[] = $line;
                continue;
            } elseif ($trimmedLine === '// Modules') {
                $inAliasSection = true;
                $filteredLines[] = $line;
                continue;
            }
            
            $shouldKeep = true;
            
            // Проверяем use statements только в секции Use Modules
            if ($inUseSection) {
                foreach ($usePatterns as $pattern) {
                    if (trim($line) === $pattern) {
                        $shouldKeep = false;
                        break;
                    }
                }
            }
            
            // Проверяем aliases только в секции Modules
            if ($shouldKeep && $inAliasSection) {
                foreach ($aliasPatterns as $pattern) {
                    if (strpos($line, $pattern) !== false) {
                        $shouldKeep = false;
                        break;
                    }
                }
            }
            
            if ($shouldKeep) {
                $filteredLines[] = $line;
            }
        }
        
        // Восстанавливаем файл с правильными переносами строк
        $content = implode("\n", $filteredLines);
        
        // Дополнительная очистка запятых
        $content = preg_replace('/(\s*\/\/ Modules)/', '$1', $content);
        $content = preg_replace('/(\s*\])/', '$1', $content);
        $content = preg_replace("/(\r?\n){3,}/", "\n\n", $content);
        
        return file_put_contents($filePath, $content) !== false;
    }
}