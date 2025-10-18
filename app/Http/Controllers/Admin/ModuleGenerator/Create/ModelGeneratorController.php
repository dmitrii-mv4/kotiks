<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModelGeneratorController extends CreateModuleController
{
    /**
     * Создаём папку для моделей Modules если не существует
     */
    private function ensureModulesModelDir()
    {
        $modulesModelPath = app_path('Models/Modules');
        if (!File::exists($modulesModelPath)) {
            File::makeDirectory($modulesModelPath, 0755, true);
        }
        return $modulesModelPath;
    }

    /**
     * Создаём модель Main для модуля
     * Главная модель которая отвечает за информацию о модуле
     */
    public function createModelMain($modelNameMain, $tableNameMain, $validated)
    {
        // 1. Создаем папку Modules если нужно
        $modelDir = $this->ensureModulesModelDir();
        
        // 2. Создаем файл модели в правильной папке
        $modelPath = app_path("Models/Modules/$modelNameMain.php");
        
        $content = <<<PHP
<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;

class $modelNameMain extends Model
{
    protected \$table = '$tableNameMain';
    protected \$guarded = false;

    protected \$fillable = [
        'name',
        'code',
    ];
}
PHP;

        File::put($modelPath, $content);
        
        if (!File::exists($modelPath)) {
            throw new \Exception("Файл модели не найден: ".$modelPath);
        }
        
        return "Модель создана: " . $modelPath;
    }

    /**
     * Создаём модель для названий свойств
     */
//     public function createModelPropertiesTitle($modelNamePropertiesTitle, $tableNamePropertiesTitle, $validated)
//     {
//         $modelDir = $this->ensureModulesModelDir();
//         $modelPath = app_path("Models/Modules/$modelNamePropertiesTitle.php");
        
//         $content = <<<PHP
// <?php

// namespace App\Models\Modules;

// use Illuminate\Database\Eloquent\Model;

// class $modelNamePropertiesTitle extends Model
// {
//     protected \$table = '$tableNamePropertiesTitle';
//     protected \$guarded = false;

//     protected \$fillable = [
//         'title',
//         'code',
//     ];
// }
// PHP;

//         File::put($modelPath, $content);
        
//         return "Модель создана: " . $modelPath;
//     }

    /**
     * Создаём модель для модуля с записями
     */
    public function createModel($modelName, $tableName, $validated)
    {
        $modelDir = $this->ensureModulesModelDir();
        $modelPath = app_path("Models/Modules/$modelName.php");

        // Формируем массив fillable полей
        $fillableFields = [];
        
        if (isset($validated['code_property']) && is_array($validated['code_property'])) {
            foreach ($validated['code_property'] as $field) {
                $field = trim($field);
                if (!empty($field)) {
                    $fillableFields[] = $field;
                }
            }
        }

        // Формируем строку с полями для fillable
        $fillableString = "";
        foreach ($fillableFields as $field) {
            $fillableString .= "        '$field',\n";
        }
        
        $fillableString = rtrim($fillableString, ",\n");

        $content = <<<PHP
<?php

namespace App\Models\Modules;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class $modelName extends Model
{
    use SoftDeletes;

    protected \$table = '$tableName';
    protected \$guarded = false;

    protected \$fillable = [
$fillableString
    ];
}
PHP;

        File::put($modelPath, $content);
        
        return "Модель создана: " . $modelPath;
    }
}
