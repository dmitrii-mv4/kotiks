<?php

namespace App\Http\Controllers\Admin\ModuleGenerator\Create;

use App\Http\Controllers\Admin\ModuleGenerator\CreateModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ViewsGeneratorController extends CreateModuleController
{
    /**
     * Создание директории если не существует
     */
    private function modulesViewsDir($moduleNameCode = null)
    {
        $viewsPath = resource_path('views/admin/modules/');

        // Проверяем существование папки modules
        if (!File::exists($viewsPath)) {
            // Создаем папку modules вместе со всеми необходимыми родительскими папками
            File::makeDirectory($viewsPath, 0755, true);
        }

        // Если передан код модуля, создаем и возвращаем путь к папке модуля
        if ($moduleNameCode) {
            $modulePath = $viewsPath . $moduleNameCode . '/';
            
            // Создаем папку для конкретного модуля
            if (!File::exists($modulePath)) {
                File::makeDirectory($modulePath, 0755, true);
            }
            
            return $modulePath;
        }

        return $viewsPath;
    }

    /**
     * Главная страница модуля
     */
    public function createViewsIndex($validated)
    {
        $moduleNameCode = $validated['code'];

        // Создаем папку Modules если нужно и получаем путь к папке модуля
        $moduleDir = $this->modulesViewsDir($moduleNameCode);

        // Определяем содержимое ДО его использования
        $content = <<<'BLADE'
@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">{{ $moduleData['name'] }}</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $moduleData['name'] }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="content">
        <div class="row">
            <div class="col-md-6 col-xl-3">
              <a class="block block-rounded block-link-pop" href="javascript:void(0)">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                  <div class="me-3">
                    <p class="fs-3 fw-medium mb-0">
                      {{ $items->count() }}
                    </p>
                    <p class="text-muted mb-0">
                      Всего записей
                    </p>
                  </div>
                  <div>
                    <i class="fa fa-2x fa-box text-warning"></i>
                  </div>
                </div>
              </a>
            </div>
        </div>

        <a href="{{ route('admin.modules.' . $moduleData['code'] . '.create') }}">
            <button type="button" class="btn btn-alt-success me-1 mb-3">
                <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Добавить запись
            </button>
        </a>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Записи в модуле</h3>
            </div>
            <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 10%;">#</th>
                                <th style="width: 70%;">Название</th>
                                <th class="text-center" style="width: 100px;">Опции</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td class="text-center">
                                    {{ $item->id }}
                                </td>
                                @if($singleColumnName)
                                    <td>{{ $item->{$singleColumnName} }}</td>
                                @else
                                    <td>-</td> <!-- Если столбца нет -->
                                @endif
                                <td class="text-center">
                                    <a href="{{ route('admin.modules.'.$moduleData->code.'.edit', $item->id) }}" type="button"
                                        class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled"
                                        data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.modules.' . $moduleData->code . '.delete', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <input type="hidden" name="module_id" value="{{ $item->id }}">

                                        <button type="submit"
                                            class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled"
                                            data-bs-toggle="tooltip" aria-label="Delete"
                                            data-bs-original-title="Delete">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

BLADE;

        // Создаём файл index
        $fullPath = $moduleDir . '/index.blade.php';

        // Создаем файл и записываем содержимое
        if (!file_put_contents($fullPath, $content) !== false) 
        {
            throw new \Exception("Ошибка создания view index для модуля: " . $moduleNameCode);
        }

        // Создаём название view файла и возвращаем
        return 'admin.modules.' . $moduleNameCode . '.index';
    }

    /**
     * Страница создания записей в модуле
     */
    public function createViewsCreate($validated)
    {
        $moduleNameCode = $validated['code'];

        // Создаем папку Modules если нужно и получаем путь к папке модуля
        $moduleDir = $this->modulesViewsDir($moduleNameCode);

        // Определяем содержимое ДО его использования
    $content = <<<'BLADE'
@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Добавление записи</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.modules.' . $moduleData['code'] . '.index') }}">{{ $moduleData['name'] }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Добавление записи</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="content">
        <div class="block block-rounded">
            <div class="block-content">
                <form action="{{ route('admin.modules.'. $moduleData['code'] . '.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')
                    <!-- Basic Elements -->
                    @foreach ($columnsDetails as $columnName => $columnType)
                        <div class="row push">
                            <div class="col-lg-3">
                                <label for="{{ $columnName }}">{{ trans("modules/{$moduleData->code}.{$columnName}") }}</label>
                            </div>
                            <div class="col-lg-8 col-xl-5">
                                <div class="mb-4">
                                    @if($columnType == 'varchar' || $columnType == 'string')
                                        <input type="text" class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" value="{{ old($columnName) }}">
                                    @elseif($columnType == 'text')
                                        <textarea class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" rows="4">{{ old($columnName) }}</textarea>
                                    @elseif($columnType == 'int')
                                        <input type="number" class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" value="{{ old($columnName) }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button class="btn btn-alt-success me-1 mb-3">
                        <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Добавить
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

BLADE;

        // Создаём файл index
        $fullPath = $moduleDir . '/create.blade.php';

        // Создаем файл и записываем содержимое
        if (!file_put_contents($fullPath, $content) !== false) 
        {
            throw new \Exception("Ошибка создания view create для модуля: " . $moduleNameCode);
        }

        // Создаём название view файла и возвращаем
        return 'admin.modules.' . $moduleNameCode . '.create';
    }

    /**
     * Страница изменения записей в модуле
     */
    public function editViewsCreate($validated)
    {
        $moduleNameCode = $validated['code'];

        // Создаем папку Modules если нужно и получаем путь к папке модуля
        $moduleDir = $this->modulesViewsDir($moduleNameCode);

        // Определяем содержимое ДО его использования
        $content = <<<'BLADE'
@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Редактирование записи</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.modules.' . $moduleData['code'] . '.index') }}">{{ $moduleData['name'] }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Редактирование записи</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <div class="content">
        <div class="block block-rounded">
            <div class="block-content">
                <form action="{{ route('admin.modules.'. $moduleData['code'] . '.update', $news->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')
                    <!-- Basic Elements -->
                    @foreach ($columnsDetails as $columnName => $columnType)
                        <div class="row push">
                            <div class="col-lg-3">
                                <label for="{{ $columnName }}">{{ trans("modules/{$moduleData->code}.{$columnName}") }}</label>
                            </div>
                            <div class="col-lg-8 col-xl-5">
                                <div class="mb-4">
                                    @if($columnType == 'varchar' || $columnType == 'string')
                                        <input type="text" class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" value="{{ $news->$columnName }}">
                                    @elseif($columnType == 'text')
                                        <textarea class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" rows="4">{{ $news->$columnName }}</textarea>
                                    @elseif($columnType == 'int')
                                        <input type="number" class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" value="{{ $news->$columnName }}">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <button class="btn btn-alt-success me-1 mb-3">
                        <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Изменить
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

BLADE;

        // Создаём файл index
        $fullPath = $moduleDir . '/edit.blade.php';

        // Создаем файл и записываем содержимое
        if (!file_put_contents($fullPath, $content) !== false) 
        {
            throw new \Exception("Ошибка создания view edit для модуля: " . $moduleNameCode);
        }

        // Создаём название view файла и возвращаем
        return 'admin.modules.' . $moduleNameCode . '.edit';
    }
}