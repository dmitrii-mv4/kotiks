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
     * Создаём папку для миграций modules если не существует
     */
    private function ensureModulesMigrationDir()
    {
        $modulesMigrationPath = database_path('migrations/modules');
        if (!File::exists($modulesMigrationPath)) {
            File::makeDirectory($modulesMigrationPath, 0755, true);
        }
        return $modulesMigrationPath;
    }

    /**
     * Cоздаём миграцию Main
     */
    public function createMigrationMain($tableNameMain, $migrationNameMain, $validated)
    {
        // 1. Создаем папку modules если нужно
        $migrationPath = $this->ensureModulesMigrationDir();
        
        // 2. Создаем файл миграции в папке modules с правильным путем
        Artisan::call('make:migration', [
            'name' => $migrationNameMain,
            '--create' => $tableNameMain,
            '--path' => 'database/migrations/modules' // Указываем подпапку
        ]);

        // 3. Ищем созданный файл в ПРАВИЛЬНОЙ папке
        $migrationFiles = File::files(database_path('migrations/modules'));
        
        $latestMigration = null;
        $latestTime = 0;
        
        // Ищем самый новый файл, содержащий указанное имя
        foreach ($migrationFiles as $file) {
            $filename = $file->getFilename();
            if (str_contains($filename, $migrationNameMain)) {
                $fileTime = $file->getCTime();
                if ($fileTime > $latestTime) {
                    $latestTime = $fileTime;
                    $latestMigration = $file->getPathname();
                }
            }
        }

        // Если не нашли, выводим отладочную информацию
        // if (!$latestMigration) {
        //     $availableFiles = array_map(function($file) {
        //         return $file->getFilename();
        //     }, $migrationFiles);
            
        //     throw new \Exception("Не удалось найти созданный файл миграции: " . $migrationNameMain . 
        //                        ". Искали в: " . $migrationPath . 
        //                        ". Доступные файлы: " . implode(', ', $availableFiles));
        // }

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

        // 5. Записываем изменения в файл
        File::put($latestMigration, $newContent);
        
        // 6. Выполняем миграцию из папки modules
        $migrationFileName = basename($latestMigration);
        Artisan::call('migrate', [
            '--path' => "database/migrations/modules/$migrationFileName"
        ]);
        
        // 7. ВСТАВЛЯЕМ ДАННЫЕ В ТАБЛИЦУ
        try {
            DB::table($tableNameMain)->insert([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при вставке данных в таблицу: " . $e->getMessage());
        }
        
        return "Миграция создана, выполнена и данные добавлены: " . $latestMigration;
    }

    /**
     * Создаём миграцию для записей
     */
    public function createMigration($migrationName, $tableName, $validated)
    {
        // 1. Создаем файл миграции
        Artisan::call('make:migration', [
            'name' => $migrationName,
            '--create' => $tableName,
            '--path' => 'database/migrations/modules' // Указываем подпапку
        ]);

        $migrationPath = database_path('migrations/modules');
        $migrationFiles = File::files(database_path('migrations/modules'));
        
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
            '--path' => "database/migrations/modules/$migrationFileName" // :cite[1]:cite[6]
        ]);
        
        return "Миграция создана и выполнена: " . $latestMigration;
    }
}