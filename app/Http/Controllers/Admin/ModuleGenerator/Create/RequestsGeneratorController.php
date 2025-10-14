<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RequestsGeneratorController extends CreateModuleController
{
    /**
     * Создание директории если не существует
     */
    private function modulesRequestsDir($moduleNameCode = null)
    {
        $requestsPath = app_path('Http/Requests/Modules/');

        // Проверяем существование папки modules
        if (!File::exists($requestsPath)) {
            // Создаем папку modules вместе со всеми необходимыми родительскими папками
            File::makeDirectory($requestsPath, 0755, true);
        }

        // Если передан код модуля, создаем и возвращаем путь к папке модуля
        if ($moduleNameCode) {
            $modulePath = $requestsPath . $moduleNameCode . '/';
            
            // Создаем папку для конкретного модуля
            if (!File::exists($modulePath)) {
                File::makeDirectory($modulePath, 0755, true);
            }
            
            return $modulePath;
        }

        return $requestsPath;
    }

    public function createRequest($validated)
    {
        // Получаем данные
        $moduleName = Str::studly($validated['code']);
        $fields = (array) $validated['code_property'];
        
        // Формируем имя класса
        $className = Str::studly($moduleName) . 'CreateRequest';
        
        // Создаем правила валидации для каждого поля
        $rulesArray = [];
        foreach ($fields as $field) {
            $rulesArray[] = "            '$field' => 'sometimes|nullable|string',";
        }
        $rulesString = implode("\n", $rulesArray);

        // Создаем папку Modules если нужно и получаем путь к папке модуля
        $moduleDirectory = $this->modulesRequestsDir($moduleName);

        // Создаем содержимое класса
        $content = <<<EOT
<?php

namespace App\Http\Requests\Modules\\$moduleName;

use Illuminate\Foundation\Http\FormRequest;

class $className extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
$rulesString
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Добавьте кастомные сообщения при необходимости
        ];
    }
}
EOT;

        /// Сохраняем файл в директорию модуля
        $filePath = $moduleDirectory . '/' . $className . '.php';
        File::put($filePath, $content);
        
        return $filePath;
    }

    public function updateRequest($validated)
    {
        // Получаем данные
        $moduleName = Str::studly($validated['code']);
        $fields = (array) $validated['code_property'];
        
        // Формируем имя класса
        $className = Str::studly($moduleName) . 'UpdateRequest';
        
        // Создаем правила валидации для каждого поля
        $rulesArray = [];
        foreach ($fields as $field) {
            $rulesArray[] = "            '$field' => 'sometimes|nullable|string',";
        }
        $rulesString = implode("\n", $rulesArray);

        // Создаем папку Modules если нужно и получаем путь к папке модуля
        $moduleDirectory = $this->modulesRequestsDir($moduleName);

        // Создаем содержимое класса
        $content = <<<EOT
<?php

namespace App\Http\Requests\Modules\\$moduleName;

use Illuminate\Foundation\Http\FormRequest;

class $className extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
$rulesString
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Добавьте кастомные сообщения при необходимости
        ];
    }
}
EOT;

        /// Сохраняем файл в директорию модуля
        $filePath = $moduleDirectory . '/' . $className . '.php';
        File::put($filePath, $content);
        
        return $filePath;
    }
}