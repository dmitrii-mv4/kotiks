<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModuleGenerator extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $fillable = [
        'name',
        'code',
        'properties',
    ];

    /**
     * Получить все таблицы модулей из базы данных
     */
    public static function getModuleTables()
    {
        return DB::select("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_NAME LIKE '%\_\_module%' 
            AND TABLE_SCHEMA = DATABASE()
        ");
    }

    /**
     * Получить данные из всех таблиц модулей
     */
    public static function getAllModuleData()
    {
        $tables = self::getModuleTables();
        $allModuleData = [];

        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            
            // Проверяем существование колонок перед запросом
            if (self::tableHasColumns($tableName, ['name', 'code'])) {
                $data = DB::table($tableName)
                    ->select('name', 'code')
                    ->get();
                
                $allModuleData[$tableName] = $data;
            }
        }

        return $allModuleData;
    }

    /**
     * Проверить наличие колонок в таблице
     */
    protected static function tableHasColumns($tableName, $columns)
    {
        try {
            $tableColumns = DB::getSchemaBuilder()->getColumnListing($tableName);
            
            foreach ($columns as $column) {
                if (!in_array($column, $tableColumns)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
