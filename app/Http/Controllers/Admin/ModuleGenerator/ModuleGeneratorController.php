<?php

namespace App\Http\Controllers\Admin\ModuleGenerator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ModulesGenerator\ModulesGeneratorCreate;
use App\Models\ModuleGenerator;

class ModuleGeneratorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        // Получаем все данные через модель
        $allModuleData = ModuleGenerator::getAllModuleData();

        return view('admin/module_generator/index', compact('allModuleData'));
    }

    public function moduleIndex(Request $request)
    {
        $urlModule = $request->path();
        $segments = explode('/', $urlModule);
        $parts = array_pad($segments, 3, null);
        $urlParts = array_slice($parts, 0, 3);

        // Проверяем, получено ли имя модуля
        if (empty($urlParts[2])) {
            return redirect()->back()->with('error', "Не удалось определить модуль из URL.");
        }

        $moduleName = $urlParts[2];
        $modelName = ucfirst($moduleName) . 'MainModule';
        $modelClass = 'App\\Models\\' . $modelName;
        
        // Проверяем существование класса модели с помощью class_exists():cite[2]:cite[4]
        if (class_exists($modelClass)) {
            $model = new $modelClass();
            $moduleData = $model->all(); // Или ваша логика получения данных
            $moduleData = $moduleData[0];
            
            // Формируем путь к view и проверяем его существование
            // $viewPath = 'admin.modules.' . $moduleName . '.index'; // Используем точечную нотацию
            
            // if (!View::exists($viewPath)) {
            //     // Если view не найден, возвращаем ошибку
            //     return redirect()->back()->with('error', "Шаблон для модуля '{$moduleName}' не найден.");
            // }
            
            // return view($viewPath, compact('moduleData'));
        }
        
        // Если модель не найдена
        return redirect()->back()->with('error', "Модель '{$modelName}' не найдена");
    }

    public function moduleCreate(Request $request)
    {
        $urlModule = $request->path();
        $segments = explode('/', $urlModule);
        $parts = array_pad($segments, 4, null);
        $urlParts = array_slice($parts, 0, 4);

        // Проверяем, получено ли имя модуля
        if (empty($urlParts[2])) {
            return redirect()->back()->with('error', "Не удалось определить модуль из URL.");
        }

        $moduleName = $urlParts[2];
        $modelName = ucfirst($moduleName) . 'MainModule';
        $modelClass = 'App\\Models\\' . $modelName;
        
        // Проверяем существование класса модели с помощью class_exists():cite[2]:cite[4]
        if (class_exists($modelClass)) {
            $model = new $modelClass();
            $moduleData = $model->all(); // Или ваша логика получения данных
            $moduleData = $moduleData[0];
            
            // Формируем путь к view и проверяем его существование
            $viewPath = 'admin.modules.' . $moduleName . '.create'; // Используем точечную нотацию
            
            if (!View::exists($viewPath)) {
                // Если view не найден, возвращаем ошибку
                return redirect()->back()->with('error', "Шаблон для модуля '{$moduleName}' не найден.");
            }
            
            return view($viewPath, compact('moduleData'));
        }
        
        // Если модель не найдена
        return redirect()->back()->with('error', "Модель '{$modelName}' не найдена");
    }
}