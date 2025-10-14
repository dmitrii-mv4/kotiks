<?php

namespace App\Http\Controllers\Admin\ModuleGenerator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ModulesGenerator\ModulesGeneratorCreate;
use App\Models\ModuleGenerator;

use App\Http\Controllers\Admin\ModuleGenerator\Create\MigrationGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\Create\ModelGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\Create\ViewsGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\Create\RequestsGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\Create\ControllerGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\Create\LangGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\Create\ControllerApiGeneratorController;
use App\Http\Controllers\Admin\ModuleGenerator\Create\RoutesApiGeneratorController;

class CreateModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function create()
    {
        return view('admin/module_generator/create');
    }

    public function store(ModulesGeneratorCreate $request)
    {
        $validated = $request->validated();

        // Формируем имена
        $tableNameMain = Str::snake($validated['code'] . '_main__module');
        $migrationNameMain = 'create_' . $tableNameMain . '_table';
        $modelNameMain = Str::studly($validated['code'] . 'MainModule');

        $tableName = Str::snake($validated['code'] . '__module');
        $migrationName = 'create_' . $tableName . '_table';
        $modelName = Str::studly($validated['code'] . 'Module');

        $controllerName = Str::studly($validated['code'] . 'Controller' );

        // Подключаем классы генератеров
        $migrationGenerator = new MigrationGeneratorController();
        $modelGenerator = new ModelGeneratorController();
        $viewsGenerator = new ViewsGeneratorController();
        $requestGenerator = new RequestsGeneratorController();
        $controllerGenerator = new ControllerGeneratorController();
        $langsGenerator = new LangGeneratorController();
        $controllerAPIGenerator = new ControllerApiGeneratorController();
        $routesAPIGenerator = new RoutesApiGeneratorController();

        // Генерируем миграцию и модель для Main
        $migrationGenerator->createMigrationMain($tableNameMain, $migrationNameMain, $validated);
        $modelGenerator->createModelMain($modelNameMain, $tableNameMain, $validated);

        // Генерируем миграцию и модель для записей
        $migrationGenerator->createMigration($migrationName, $tableName, $validated);
        $modelGenerator->createModel($modelName, $tableName, $validated);

        // Генерируем Views и возвращаем название файла view
        $indexViewName = $viewsGenerator->createViewsIndex($validated);
        $createViewName = $viewsGenerator->createViewsCreate($validated);
        $updateViewName = $viewsGenerator->editViewsCreate($validated);

        // Генерируем Request
        $requestGenerator->createRequest($validated);
        $requestGenerator->updateRequest($validated);

        // Генерируем контроллер
        $controllerGenerator->createController($controllerName, $modelNameMain, $modelName, $indexViewName, $createViewName, $updateViewName, $validated);

        // Генерируем lang файлы
        $langsGenerator->createLangs($validated);

        // Генерируем контроллер на API
        $controllerAPIGenerator->createControllerAPI($controllerName, $modelName, $validated);

        // Генерируем роутер файлы
        $routesAPIGenerator->createRoutesAPI($validated);


        return redirect()->route('admin.modules')->with('success', 'Модуль, миграция и запись созданы успешно.');

        //return redirect()->back()->with('success', 'Модуль, миграция и запись созданы успешно.');
        //return redirect()->route('admin.modules')->with('success', 'Модуль, миграция и запись созданы успешно.');
    }

    public function delete($module)
    {
        try {
            // Формируем имена таблиц для поиска
            $tableSuffix = $module . '__module';
            $tableMainSuffix = $module . '_main__module';
            
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
                ->where('migration', 'LIKE', "%{$module}%")
                ->delete();
            
            // Включаем проверку внешних ключей обратно
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            // === Удаление миграций
            $migrationPath = database_path('migrations/modules/');

            // Ищем файлы по паттернам
            $mainModulePattern = '*'.$module.'_main__module_table.php';
            $modulePattern = '*'.$module.'__module_table.php';

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
            $modelModule = Str::studly($module);

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

            // === Удаление контроллера ===
            $controllerModule = Str::studly($module);

            $controllerPaths = [
                app_path('Http/Controllers/Admin/Modules/'),
                app_path('Http/Controllers/'), // дополнительный путь для поиска
            ];

            $controllerFiles = [];
            $controllerPatterns = [
                '*'.$controllerModule.'Controller.php',
            ];

            $deletedControllerFiles = [];

            foreach ($controllerPaths as $controllerPath) {
                if (!File::exists($controllerPath)) {
                    continue;
                }
                
                foreach ($controllerPatterns as $pattern) {
                    $foundControllers = File::glob($controllerPath . $pattern);
                    $controllerFiles = array_merge($controllerFiles, $foundControllers);
                }
                
                // Рекурсивный поиск в поддиректориях
                $allFiles = File::allFiles($controllerPath);
                foreach ($allFiles as $file) {
                    $filename = $file->getFilename();
                    foreach ($controllerPatterns as $pattern) {
                        if (fnmatch($pattern, $filename)) {
                            $controllerFiles[] = $file->getPathname();
                        }
                    }
                }
            }

            // Удаляем дубликаты
            $controllerFiles = array_unique($controllerFiles);

            // ★★ ВАЖНО: ДОБАВЛЯЕМ УДАЛЕНИЕ НАЙДЕННЫХ ФАЙЛОВ ★★
            foreach ($controllerFiles as $controllerFile) {
                if (File::exists($controllerFile)) {
                    File::delete($controllerFile);
                    $deletedControllerFiles[] = basename($controllerFile);
                }
            }
            // === end Удаление контроллера ===

            // === Удаление языковых файлов модуля ===
            $langPaths = [
                resource_path('lang/en/modules/' . $module . '.php'),
                resource_path('lang/ru/modules/' . $module . '.php'),
            ];

            $deletedLangFiles = [];

            foreach ($langPaths as $langFile) {
                if (File::exists($langFile)) {
                    File::delete($langFile);
                    $deletedLangFiles[] = basename($langFile);
                }
            }
            // === end Удаление языковых файлов ===

            // === Удаление директории шаблонов модуля ===
            $viewPath = resource_path('views/admin/modules/' . $module);

            $deletedViewDirs = [];
            if (File::exists($viewPath)) {
                // Вариант 1: Использование встроенного метода Laravel (рекомендуется)
                File::deleteDirectory($viewPath);
                $deletedViewDirs[] = $viewPath;
            }
            // === end Удаление директории шаблонов ===

            // === Удаление папки Requests модуля ===
            $requestsModulePath = app_path('Http/Requests/Modules/' . Str::studly($module));

            $deletedRequestsDirs = [];
            if (File::exists($requestsModulePath)) {
                File::deleteDirectory($requestsModulePath);
                $deletedRequestsDirs[] = $requestsModulePath;
            }
            // === end Удаление папки Requests ===

            // === Удаление router API ===
            // Формируем путь к файлу
            $routerApiFile = base_path('routes/api/modules/' . $module . '.php');

            // Проверяем существование файла ДЕЙСТВИТЕЛЬНО простым способом
            if (file_exists($routerApiFile)) {
                // Пытаемся удалить
                if (unlink($routerApiFile)) {
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
            // === end Удаление router API ===

            // Проверяем, были ли удалены какие-либо таблицы в БД
            if (empty($deletedTables)) {
                return redirect()->route('admin.modules')
                    ->with('warning', 'Не найдено таблиц для удаления с указанными суффиксами.');
            }
            
            return redirect()->route('admin.modules')
                ->with('success', 'Модуль и связанные таблицы удалены: ' . implode(', ', $deletedTables));
            
        } catch (\Exception $e) {
            // Гарантируем, что проверка внешних ключей будет включена даже при ошибке
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            return redirect()->route('admin.modules')
                ->with('error', 'Ошибка при удалении модуля: ' . $e->getMessage());
        }
    }
}
