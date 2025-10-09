<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModelGeneratorController extends CreateModuleController
{
    /**
     * Создаём модель Main для модуля
     * Главная модель которая отвечает за информацию о модуле
     */
    public function createModelMain($modelNameMain, $tableNameMain, $validated)
    {
        Artisan::call('make:model', ['name' => $modelNameMain]);

        $modelPath = app_path("Models/$modelNameMain.php");

        if (!File::exists($modelPath)) {
            throw new \Exception("Файл модели не найден: ".$modelPath);
        }

        $content = <<<PHP
<?php

namespace App\Models;

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
    }

    /**
     * Создаём модель для названий свойств
     */
    public function createModelPropertiesTitle($modelNamePropertiesTitle, $tableNamePropertiesTitle, $validated)
    {
        Artisan::call('make:model', ['name' => $modelNamePropertiesTitle]);

        $modelPath = app_path("Models/$modelNamePropertiesTitle.php");

        if (!File::exists($modelPath)) {
            throw new \Exception("Файл модели не найден: ".$modelPath);
        }

        $content = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class $modelNamePropertiesTitle extends Model
{
    protected \$table = '$tableNamePropertiesTitle';
    protected \$guarded = false;

    protected \$fillable = [
        'title',
        'code',
    ];
}
PHP;

        File::put($modelPath, $content);
    }

    /**
     * Создаём модель для модуля с записями
     */
    public function createModel($modelName, $tableName, $validated)
    {
        Artisan::call('make:model', ['name' => $modelName]);

        $modelPath = app_path("Models/$modelName.php");

        if (!File::exists($modelPath)) {
            throw new \Exception("Файл модели не найден: ".$modelPath);
        }

        // Формируем массив fillable полей
        $fillableFields = [];
        
        if (isset($validated['code_property']) && is_array($validated['code_property'])) {
            foreach ($validated['code_property'] as $field) {
                // Убираем лишние пробелы и добавляем в массив
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
        
        // Убираем последнюю запятую и перенос строки
        $fillableString = rtrim($fillableString, ",\n");

        $content = <<<PHP
<?php

namespace App\Models;

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
    }
}