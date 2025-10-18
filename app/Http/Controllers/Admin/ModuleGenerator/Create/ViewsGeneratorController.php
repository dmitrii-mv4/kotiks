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
                      {{ $items->total() }}
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
            <div class="col-md-6 col-xl-3">
              <a href="/api/{{ $moduleData['code'] }}" target="_blank" class="block block-rounded block-link-pop">
                <div class="block-content block-content-full d-flex align-items-center justify-content-between">
                  <div class="me-3">
                    <p class="fs-3 fw-medium mb-0">
                      API модуля
                    </p>
                    <p class="text-muted mb-0">
                      /api/{{ $moduleData['code'] }}
                    </p>
                  </div>
                  <div>
                    <i class="fa fa-2x fa-chart-area text-danger"></i>
                  </div>
                </div>
              </a>
            </div>
        </div>

        @if(auth()->user()->hasPermission('module_'.$moduleData['code'].'_create'))
            <a href="{{ route('admin.modules.' . $moduleData['code'] . '.create') }}">
                <button type="button" class="btn btn-alt-success me-1 mb-3">
                    <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Добавить запись
                </button>
            </a>
        @endif

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
                                    <div class="btn-group">

                                        @can('update', $item)
                                        <a href="{{ route('admin.modules.'.$moduleData->code.'.edit', $item->id) }}" type="button"
                                            class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled"
                                            data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        @endcan

                                        @can('delete', $item)
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
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($items->hasPages())
                  <div class="row">
                      <div class="col-sm-12 col-md-5">
                          <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">
                              Показано с {{ $items->firstItem() }} по {{ $items->lastItem() }} из {{ $items->total() }} записей
                          </div>
                      </div>
                      <div class="col-sm-12 col-md-7">
                          <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                              <ul class="pagination">
                                  {{-- Previous Page Link --}}
                                  @if ($items->onFirstPage())
                                      <li class="paginate_button page-item previous disabled" id="DataTables_Table_0_previous">
                                          <a aria-controls="DataTables_Table_0" aria-disabled="true" role="link" data-dt-idx="previous" tabindex="0" class="page-link">
                                              <i class="fa fa-angle-left"></i>
                                          </a>
                                      </li>
                                  @else
                                      <li class="paginate_button page-item previous" id="DataTables_Table_0_previous">
                                          <a href="{{ $items->previousPageUrl() }}" aria-controls="DataTables_Table_0" role="link" data-dt-idx="previous" tabindex="0" class="page-link">
                                              <i class="fa fa-angle-left"></i>
                                          </a>
                                      </li>
                                  @endif

                                  {{-- Pagination Elements --}}
                                  @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                                      @if ($page == $items->currentPage())
                                          <li class="paginate_button page-item active">
                                              <a href="#" aria-controls="DataTables_Table_0" role="link" aria-current="page" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</a>
                                          </li>
                                      @else
                                          <li class="paginate_button page-item">
                                              <a href="{{ $url }}" aria-controls="DataTables_Table_0" role="link" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</a>
                                          </li>
                                      @endif
                                  @endforeach

                                  {{-- Next Page Link --}}
                                  @if ($items->hasMorePages())
                                      <li class="paginate_button page-item next" id="DataTables_Table_0_next">
                                          <a href="{{ $items->nextPageUrl() }}" aria-controls="DataTables_Table_0" role="link" data-dt-idx="next" tabindex="0" class="page-link">
                                              <i class="fa fa-angle-right"></i>
                                          </a>
                                      </li>
                                  @else
                                      <li class="paginate_button page-item next disabled" id="DataTables_Table_0_next">
                                          <a aria-controls="DataTables_Table_0" aria-disabled="true" role="link" data-dt-idx="next" tabindex="0" class="page-link">
                                              <i class="fa fa-angle-right"></i>
                                          </a>
                                      </li>
                                  @endif
                              </ul>
                          </div>
                      </div>
                  </div>
                @endif
                <!-- end Pagination -->
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

        $moduleItemId = $moduleNameCode . '->id';

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
                <form action="{{ route('admin.modules.'. $moduleData['code'] . '.update', $item->id) }}" method="POST" enctype="multipart/form-data">
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
                                        <input type="text" class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" value="{{ $item->$columnName }}">
                                    @elseif($columnType == 'text')
                                        <textarea class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" rows="4">{{ $item->$columnName }}</textarea>
                                    @elseif($columnType == 'int')
                                        <input type="number" class="form-control" id="{{ $columnName }}" name="{{ $columnName }}" value="{{ $item->$columnName }}">
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