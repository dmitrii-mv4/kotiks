<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ControllerApiGeneratorController extends CreateModuleController
{
    public function createControllerAPI($controllerName, $modelName, $validated)
    {
        $nameModuleCode = $validated['code'];
        $nameModule = $validated['name'];

        // $validated['property']; // нужно добавить в type
        // $validated['code_property']; // нужно добавить в name там где items
        // $validated['name_property']; // нужно добавить в name_value

        // 1. Определяем путь и имя для Artisan
        $artisanControllerName = 'Api/Modules/' . $controllerName;

        // 2. Создаем необходимые директории, если их нет
        $controllerDirectory = app_path('Http/Controllers/Api/Modules');
        if (!File::isDirectory($controllerDirectory)) {
            File::makeDirectory($controllerDirectory, 0755, true);
        }

        // 3. Вызов Artisan для создания контроллера
        Artisan::call('make:controller', [
            'name' => $artisanControllerName,
        ]);

        // 4. Формируем корректный путь для проверки (используем правильные разделители)
        $controllerPath = app_path('Http/Controllers/' . str_replace('/', DIRECTORY_SEPARATOR, $artisanControllerName) . '.php');

        // Проверяем, что файл был создан
        if (!File::exists($controllerPath)) {
            throw new \Exception("Не удалось найти созданный файл контроллера: " . $controllerPath);
        }

        // 5. Определяем новое содержимое для контроллера
        $content = "<?php

namespace App\Http\Controllers\Api\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\\$modelName;
use Illuminate\Http\JsonResponse;

class $controllerName extends Controller
{
    /**
     * Получить все записи новостей для API
     */
    public function index(): JsonResponse
    {
        // Получаем данные из модели модуля
        \$moduleData = $modelName::orderBy('id')->get();

        // Получаем все данные через модель
\$moduleData = $modelName::get();
        
        // Формируем ответ с информацией о модуле и данными
        \$apiModules = [
            'module_info' => [
                'name' => '{$nameModule}',
                'code' => '{$nameModuleCode}'
            ],
            'items' => \$moduleData
        ];
        
        return response()->json(\$apiModules);
    }
}";

        // Записываем изменения в файл
        if (File::put($controllerPath, $content) === false) {
            throw new \Exception("Ошибка создания контроллера: " . $controllerPath);
        }

        return true;
    }
}