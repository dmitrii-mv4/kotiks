@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Создание модуля</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.modules') }}">Модули</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Создание модуля</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        <form action="{{ route('admin.modules.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="block block-rounded">
                <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                    <li class="nav-item" role="main">
                        <button type="button" class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main-classic" role="tab" aria-controls="main-classic" aria-selected="true">
                        Общее
                        </button>
                    </li>
                    <li class="nav-item" role="properties">
                        <button type="button" class="nav-link" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties-classic" role="tab" aria-controls="properties-classic" aria-selected="true">
                        Свойства
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" id="prermissions-tab" data-bs-toggle="tab" data-bs-target="#prermissions" role="tab" aria-controls="search-photos" aria-selected="false" tabindex="-1">
                        Полномочия
                        </button>
                    </li>
                </ul>
                <div class="block-content tab-content overflow-hidden">
                <!-- Main -->
                <div class="tab-pane fade show active" id="main-classic" role="tabpanel" aria-labelledby="main-tab" tabindex="0">
                    <div class="row">

                        <div class="block block-rounded">
                            <div class="block-content">
                                
                                <!-- Basic Elements -->
                                <div class="row push">
                                    <div class="col-lg-3">
                                        <label class="form-label" for="example-text-input">Название:</label>
                                    </div>
                                    <div class="col-lg-8 col-xl-5">
                                        <div class="mb-4">
                                            <input type="text" class="@error('name') is-invalid @enderror form-control" id="example-text-input" name="name" placeholder="" value="{{ old('name') }}">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row push">
                                    <div class="col-lg-3">
                                        <label class="form-label" for="example-text-input">Код:</label>
                                    </div>
                                    <div class="col-lg-8 col-xl-5">
                                        <div class="mb-4">
                                            <input type="text" class="@error('code') is-invalid @enderror form-control" id="example-text-input" name="code" placeholder="" value="{{ old('code') }}">
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    
                    </div>
                </div>
                <!-- END Main -->

                <!-- Properties -->
                <div class="tab-pane fade show" id="properties-classic" role="tabpanel" aria-labelledby="properties-tab" tabindex="0">
                    <div class="row">
                        <div class="block block-rounded">
                            <div class="block-content">
                                
                                <!-- Контейнер для строк -->
                                <div id="properties-container">
                                    <!-- Basic Elements -->
                                    <div class="row push property-row first-row">
                                        <div class="col-lg-8 col-xl-1">
                                            <div class="mb-4 row-number">
                                                1
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xl-3">
                                            <div class="mb-4">
                                                <input type="text" class="@error('name_property.*') is-invalid @enderror form-control" 
                                                    name="name_property[]" placeholder="Название" 
                                                    value="{{ old('name_property.0', isset($properties[0]['name_property']) ? $properties[0]['name_property'] : '') }}">
                                                @error('name_property.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xl-3">
                                            <div class="mb-4">
                                                <select class="@error('property.*') is-invalid @enderror form-control form-select" 
                                                        name="property[]" aria-label="Floating label select example">
                                                    <option selected disabled>Выберите свойство</option>
                                                    <option value="string" {{ (old('property.0', isset($properties[0]['property']) ? $properties[0]['property'] : '') == 'string') ? 'selected' : '' }}>Строка</option>
                                                    <option value="text" {{ (old('property.0', isset($properties[0]['property']) ? $properties[0]['property'] : '') == 'text') ? 'selected' : '' }}>Текст</option>
                                                    <option value="integer" {{ (old('property.0', isset($properties[0]['property']) ? $properties[0]['property'] : '') == 'integer') ? 'selected' : '' }}>Число</option>
                                                </select>
                                                @error('property.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xl-3">
                                            <div class="mb-4">
                                                <input type="text" class="@error('code_property.*') is-invalid @enderror form-control" 
                                                    name="code_property[]" placeholder="Код" 
                                                    value="{{ old('code_property.0', isset($properties[0]['code_property']) ? $properties[0]['code_property'] : '') }}">
                                                @error('code_property.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-xl-1">
                                            <div class="mb-4">
                                                <!-- У первой строки нет кнопки удаления -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Кнопка добавления новой строки -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success add-property-row">
                                            + Добавить свойство
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Добавление новой строки свойств
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('add-property-row')) {
                            const container = document.getElementById('properties-container');
                            const rowCount = document.querySelectorAll('.property-row').length;
                            
                            const newRow = `
                                <div class="row push property-row">
                                    <div class="col-lg-8 col-xl-1">
                                        <div class="mb-4 row-number">
                                            ${rowCount + 1}
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-xl-3">
                                        <div class="mb-4">
                                            <input type="text" class="form-control" name="name_property[]" placeholder="Название" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-xl-3">
                                        <div class="mb-4">
                                            <select class="form-control form-select" name="property[]">
                                                <option selected disabled>Выберите свойство</option>
                                                <option value="string">Строка</option>
                                                <option value="text">Текст</option>
                                                <option value="integer">Число</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-xl-3">
                                        <div class="mb-4">
                                            <input type="text" class="form-control" name="code_property[]" placeholder="Код" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-xl-1">
                                        <div class="mb-4">
                                            <button type="button" class="btn btn-danger remove-row">×</button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            container.insertAdjacentHTML('beforeend', newRow);
                        }
                        
                        // Удаление строки (только для не-первых строк)
                        if (e.target.classList.contains('remove-row')) {
                            const row = e.target.closest('.property-row');
                            // Проверяем, что это не первая строка
                            if (!row.classList.contains('first-row')) {
                                row.remove();
                                // Обновляем нумерацию
                                document.querySelectorAll('.property-row').forEach((row, index) => {
                                    row.querySelector('.row-number').textContent = index + 1;
                                });
                            }
                        }
                    });
                });
                </script>

                <style>
                .remove-row {
                    background: #dc3545;
                    color: white;
                    border: none;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    font-size: 18px;
                    line-height: 1;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .remove-row:hover {
                    background: #c82333;
                }

                .add-property-row {
                    margin-top: 15px;
                    float: right;
                }
                </style>
                <!-- END Properties -->

                <!-- Prermissions -->
                <div class="tab-pane fade" id="prermissions" role="tabpanel" aria-labelledby="prermissions-tab" tabindex="0">
                    <div class="row g-sm push">
                    
                        <div class="block-content">

                            <!-- Basic Prermissions -->
                            <h2 class="content-heading pt-0">Общее</h2>
                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-default1" name="show_admin">
                                    <label class="form-check-label" for="access-admin-checkbox-default1">Доступ к панели администратора</label>
                                </div>
                            </div>
                            <!-- END Basic Prermissions --> 

                            <!-- Users Prermissions -->
                            <h2 class="content-heading pt-0 mt-5">Пользователи</h2>
                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-users_viewAny" name="users_viewAny">
                                    <label class="form-check-label" for="access-admin-checkbox-users_viewAny">Просмотр всех пользователей</label>
                                </div>
                            </div>

                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-users_view" name="users_view">
                                    <label class="form-check-label" for="access-admin-checkbox-users_view">Просмотр профиль пользователя</label>
                                </div>
                            </div>

                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-users_create" name="users_create">
                                    <label class="form-check-label" for="access-admin-checkbox-users_create">Создание пользователя</label>
                                </div>
                            </div>

                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-users_update" name="users_update">
                                    <label class="form-check-label" for="access-admin-checkbox-users_update">Редактирование пользователя</label>
                                </div>
                            </div>

                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-users_delete" name="users_delete">
                                    <label class="form-check-label" for="access-admin-checkbox-users_delete">Удаление пользователя</label>
                                </div>
                            </div>
                            <!-- END Users Prermissions --> 

                            <!-- Roles Prermissions -->
                            <h2 class="content-heading pt-0 mt-5">Роли</h2>
                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-roles_viewAny" name="roles_viewAny">
                                    <label class="form-check-label" for="access-admin-checkbox-roles_viewAny">Просмотр всех ролей</label>
                                </div>
                            </div>

                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-roles_create" name="roles_create">
                                    <label class="form-check-label" for="access-admin-checkbox-roles_create">Создание роли</label>
                                </div>
                            </div>

                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-roles_update" name="roles_update">
                                    <label class="form-check-label" for="access-admin-checkbox-roles_update">Редактирование роли</label>
                                </div>
                            </div>

                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="access-admin-checkbox-roles_delete" name="roles_delete">
                                    <label class="form-check-label" for="access-admin-checkbox-roles_delete">Удаление роли</label>
                                </div>
                            </div>

                            <!-- END Users Prermissions --> 
                        </div>

                    </div>

                </div>
                <!-- END Photos -->
                    <button class="btn btn-alt-success me-1 mb-3">
                        <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Добавить
                    </button>

                </div>

            </div>
        </form>
    
    </div>
    <!-- END Page Content -->

@endsection
