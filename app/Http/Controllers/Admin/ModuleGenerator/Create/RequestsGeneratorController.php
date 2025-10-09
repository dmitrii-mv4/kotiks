<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RequestsGeneratorController extends CreateModuleController
{
    public function createRequest($validated)
    {
        // Получаем данные
        $moduleName = Str::studly($validated['code']);
        $fields = (array) $validated['code_property'];
        
        // Формируем имя класса
        $className = Str::studly($moduleName) . 'RequestsCreate';
        
        // Создаем правила валидации для каждого поля
        $rulesArray = [];
        foreach ($fields as $field) {
            $rulesArray[] = "            '$field' => 'sometimes|nullable|string',";
        }
        $rulesString = implode("\n", $rulesArray);

        // Создаем директорию для модуля
        $moduleDirectory = app_path('Http/Requests/Modules/' . $moduleName);
        File::ensureDirectoryExists($moduleDirectory);
        
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