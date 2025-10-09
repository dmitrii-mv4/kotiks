<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MigrationGeneratorController extends CreateModuleController
{
    /**
     * Cоздаём миграцию Main
     */
    public function createMigrationMain($tableNameMain, $migrationNameMain, $validated)
    {
    // 1. Создаем файл миграции
    Artisan::call('make:migration', [
        'name' => $migrationNameMain,
        '--create' => $tableNameMain
    ]);

    $migrationPath = database_path('migrations');
    $migrationFiles = File::files($migrationPath);
    
    $latestMigration = null;
    
    // 2. Ищем созданный файл по имени и таблице
    foreach ($migrationFiles as $file) {
        $filename = $file->getFilename();
        if (str_contains($filename, $migrationNameMain)) {
            $latestMigration = $file->getPathname();
            break;
        }
    }

    if (!$latestMigration) {
        throw new \Exception("Не удалось найти созданный файл миграции: " . $migrationNameMain);
    }

    // 3. Полностью перезаписываем содержимое файла
    $newContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('$tableNameMain', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->string('code')->unique();
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('$tableNameMain');
    }
};
PHP;

    // 4. Записываем изменения в файл
    File::put($latestMigration, $newContent);
    
    // 5. Выполняем миграцию, чтобы создать таблицу в БД
    $migrationFileName = basename($latestMigration);
    Artisan::call('migrate', [
        '--path' => "database/migrations/$migrationFileName"
    ]);
    
    // 6. ВСТАВЛЯЕМ ДАННЫЕ В ТАБЛИЦУ
    try {
        DB::table($tableNameMain)->insert([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } catch (\Exception $e) {
        // Обработка ошибок вставки
        throw new \Exception("Ошибка при вставке данных в таблицу: " . $e->getMessage());
    }
    
    return "Миграция создана, выполнена и данные добавлены: " . $latestMigration;
}

    /**
     * Создаём миграцию для названий свойств
     */
    public function createMigrationPropertiesTitle($tableNamePropertiesTitle, $migrationNamePropertiesTitle, $validated)
    {
        // 1. Создаем файл миграции
        Artisan::call('make:migration', [
            'name' => $migrationNamePropertiesTitle,
            '--create' => $tableNamePropertiesTitle // Явно указываем, что migration создает новую таблицу
        ]);

        $migrationPath = database_path('migrations');
        $migrationFiles = File::files($migrationPath);
        
        $latestMigration = null;
        
        // 2. Ищем созданный файл по имени и таблице
        foreach ($migrationFiles as $file) {
            $filename = $file->getFilename();
            if (str_contains($filename, $migrationNamePropertiesTitle)) {
                $latestMigration = $file->getPathname();
                break;
            }
        }

        if (!$latestMigration) {
            throw new \Exception("Не удалось найти созданный файл миграции: " . $migrationNamePropertiesTitle);
        }

        // 3. Полностью перезаписываем содержимое файла
        $newContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('$tableNamePropertiesTitle', function (Blueprint \$table) {
            \$table->id();
            \$table->string('title');
            \$table->string('code');
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('$tableNamePropertiesTitle');
    }
};
PHP;

        // 5. Записываем изменения в файл
        File::put($latestMigration, $newContent);
        
        // 6. ВАЖНО: Выполняем миграцию, чтобы создать таблицу в БД
        $migrationFileName = basename($latestMigration);
        // Указываем относительный путь, который Laravel понимает correctly
        Artisan::call('migrate', [
            '--path' => "database/migrations/$migrationFileName" // :cite[1]:cite[6]
        ]);

        // 7. ВСТАВЛЯЕМ ДАННЫЕ В ТАБЛИЦУ
        try {
            // Проверяем, что оба массива существуют и имеют одинаковую длину
            if (isset($validated['name_property']) && 
                isset($validated['code_property']) && 
                count($validated['name_property']) === count($validated['code_property'])) {
                
                $dataToInsert = [];
                
                // Формируем массив для массовой вставки
                foreach ($validated['name_property'] as $index => $nameProperty) {
                    // Проверяем, что существует соответствующий code_property
                    if (isset($validated['code_property'][$index])) {
                        $dataToInsert[] = [
                            'title' => $nameProperty,
                            'code' => $validated['code_property'][$index],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                
                // Выполняем массовую вставку, если есть данные
                if (!empty($dataToInsert)) {
                    DB::table($tableNamePropertiesTitle)->insert($dataToInsert);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при вставке данных в таблицу: " . $e->getMessage());
        }
        
        return "Миграция создана и выполнена: " . $latestMigration;
    }

    /**
     * Создаём миграцию для записей
     */
    public function createMigration($migrationName, $tableName, $validated)
    {
        // 1. Создаем файл миграции
        Artisan::call('make:migration', [
            'name' => $migrationName,
            '--create' => $tableName
        ]);

        $migrationPath = database_path('migrations');
        $migrationFiles = File::files($migrationPath);
        
        $latestMigration = null;
        
        // 2. Ищем созданный файл по имени и таблице
        foreach ($migrationFiles as $file) {
            $filename = $file->getFilename();
            if (str_contains($filename, $migrationName)) {
                $latestMigration = $file->getPathname();
                break;
            }
        }

        if (!$latestMigration) {
            throw new \Exception("Не удалось найти созданный файл миграции: " . $migrationName);
        }

        // 3. Формируем код для столбцов
        $columnsCode = "";
        if (isset($validated['code_property']) && isset($validated['property'])) {
            $columnNames = $validated['code_property'];
            $columnTypes = $validated['property'];
            
            foreach ($columnNames as $index => $columnName) {
                if (isset($columnTypes[$index]) && !empty(trim($columnName))) {
                    $columnType = $columnTypes[$index];
                    $columnName = trim($columnName);
                    $columnsCode .= "            \$table->$columnType('$columnName');\n";
                }
            }
        }

        // 4. Полностью перезаписываем содержимое файла
        $newContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('$tableName', function (Blueprint \$table) {
            \$table->id();
$columnsCode            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('$tableName');
    }
};
PHP;

        // 5. Записываем изменения в файл
        File::put($latestMigration, $newContent);
        
        // 6. ВАЖНО: Выполняем миграцию, чтобы создать таблицу в БД
        $migrationFileName = basename($latestMigration);
        // Указываем относительный путь, который Laravel понимает correctly
        Artisan::call('migrate', [
            '--path' => "database/migrations/$migrationFileName" // :cite[1]:cite[6]
        ]);
        
        return "Миграция создана и выполнена: " . $latestMigration;
    }
}