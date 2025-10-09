<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ControllerGeneratorController extends CreateModuleController
{
    public function createController($controllerName, $modelNameMain, $modelName, $indexViewName, $createViewName, $validated)
    {
        $controllerPath = resource_path('controller/admin/modules/');

        Artisan::call('make:controller', [
            'name' => '/Admin/Modules/' . $controllerName,
        ]);

        $controllerPath = app_path('Http/Controllers/Admin/Modules/' . $controllerName . '.php');

        // Проверяем, что файл был создан
        if (!File::exists($controllerPath)) {
            throw new \Exception("Не удалось найти созданный файл контроллера: " . $controllerPath);
        }

        $nameModuleCode = $validated['code'];
        $nameModule = Str::studly($validated['code']);
        $pathRequests = Str::studly($validated['code']) . '\\' . $nameModule . 'RequestsCreate';
        $nameRequests =  $nameModule . 'RequestsCreate';

        // Определяем содержимое ДО его использования
        $content = "<?php

namespace App\Http\Controllers\Admin\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modules\\$pathRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use App\Models\\$modelNameMain;
use App\Models\\$modelName;

class {$controllerName} extends Controller
{
    /**
     * Метод показа модуля гланой страницы
     */
    public function index(): View
    {
        // Получаем все данные через модель
        \$moduleData = $modelNameMain::get();
        \$moduleData = \$moduleData['0'];

        // Выводим все записи
        \$items = $modelName::orderBy('id')->get();

        // Получаем список всех столбцов таблицы
        \$tableName = (new $modelName)->getTable();
        \$columns = Schema::getColumnListing(\$tableName);

        // Исключаем системные столбцы и ID
        \$columnsToExclude = ['id', 'created_at', 'updated_at', 'deleted_at'];
        \$availableColumns = array_diff(\$columns, \$columnsToExclude);

        // Берём первый столбец из оставшихся
        \$singleColumnName = reset(\$availableColumns);

        // Если подходящих столбцов нет, можно установить значение null
        if (!\$singleColumnName) {
            \$singleColumnName = null;
        }

        return view('admin.modules.$indexViewName', compact('moduleData', 'items', 'singleColumnName'));
    }

    /**
     * Метод добавления записей
     */
    public function create(): View
    {
        // Получаем все данные через модель
        \$moduleData = $modelNameMain::get();
        \$moduleData = \$moduleData['0'];

        // Выводим название таблиц из БД
        \$tableName = \$moduleData['code'] . '__module';
        \$columnsNameBD = Schema::getColumnListing(\$tableName);

        // Удаляем ненужные значения
        \$columnsNameBDKill = ['id', 'created_at', 'updated_at', 'deleted_at'];
        \$columnsName = array_diff(\$columnsNameBD, \$columnsNameBDKill);

        // Проверяем, есть ли столбцы для обработки
        if (empty(\$columnsName)) {
            \$columnsDetails = [];
        } else {
            // Получаем детальную информацию о столбцах
            \$schemaBuilder = Schema::getConnection()->getSchemaBuilder();
            \$columnsDetails = [];
            
            foreach (\$columnsName as \$column) {
                try {
                    \$type = \$schemaBuilder->getColumnType(\$tableName, \$column);
                    \$columnsDetails[\$column] = \$type ?: 'unknown';
                } catch (\Exception \$e) {
                    \$columnsDetails[\$column] = 'unknown';
                }
            }
        }

        return view('admin.modules.$createViewName', compact('moduleData', 'columnsName', 'columnsDetails'));
    }

    public function store($nameRequests \$request)
    {
        \$dataValidated = \$request->validated();

       try {
            // Создаем новую запись в модели
            $modelName::create(\$dataValidated);
            
            // Перенаправляем с сообщением об успехе
            return redirect()->route('admin.modules.' . strtolower('$nameModuleCode') . '.index')
                ->with('success', 'Запись успешно добавлена.');
        } catch (\\Exception \$e) {
            // В случае ошибки возвращаем обратно с сообщением об ошибке
            return back()->withInput()
                ->with('error', 'Ошибка при добавлении записи: ' . \$e->getMessage());
        }
    }
}";
        // Записываем изменения в файл
        if (File::put($controllerPath, $content) === false) {
            throw new \Exception("Ошибка создания контроллера: " . $controllerPath);
        }
    }
}