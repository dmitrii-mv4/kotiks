@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Редактирование роли</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles') }}">Роли пользователей</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Редактирование роли</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

            <form action="{{ route('admin.roles.update', $role['id']) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="block block-rounded">
                <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active" id="role-tab" data-bs-toggle="tab" data-bs-target="#role-classic" role="tab" aria-controls="role-classic" aria-selected="true">
                    Роль
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link" id="prermissions-tab" data-bs-toggle="tab" data-bs-target="#prermissions" role="tab" aria-controls="search-photos" aria-selected="false" tabindex="-1">
                    Права доступов
                    </button>
                </li>
                </ul>
                <div class="block-content tab-content overflow-hidden">
                <!-- Role -->
                <div class="tab-pane fade show active" id="role-classic" role="tabpanel" aria-labelledby="role-tab" tabindex="0">
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
                                            <input type="text" class="@error('name') is-invalid @enderror form-control" id="example-text-input" name="name" placeholder="" value="{{ $role['name'] }}">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    
                    </div>
                </div>
                <!-- END Role -->

                <!-- Prermissions -->
                <div class="tab-pane fade" id="prermissions" role="tabpanel" aria-labelledby="prermissions-tab" tabindex="0">
                    <div class="row g-sm push">
                        <div class="block-content">

                            <!-- Basic Prermissions -->
                            <h2 class="content-heading pt-0">Общее</h2>
                            <div class="row g-sm push push">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        value="1" 
                                        id="access-admin-checkbox-show_admin" 
                                        name="show_admin"
                                        @if($role->permissions->contains('name', 'show_admin')) checked @endif>
                                    <label class="form-check-label" for="access-admin-checkbox-show_admin">Доступ к панели администратора</label>
                                </div>
                            </div>
                            <!-- END Basic Prermissions --> 

                            <!-- Users Prermissions -->
                            <h2 class="content-heading pt-0 mt-5">Пользователи</h2>
                            @php
                                $userPermissions = [
                                    'viewAny' => 'Просмотр всех пользователей',
                                    'view' => 'Просмотр профиля пользователя',
                                    'create' => 'Создание пользователя',
                                    'update' => 'Редактирование пользователя',
                                    'delete' => 'Удаление пользователя'
                                ];
                            @endphp
                            
                            @foreach($userPermissions as $action => $label)
                                <div class="row g-sm push push">
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            value="1" 
                                            id="access-admin-checkbox-users_{{ $action }}" 
                                            name="users_{{ $action }}"
                                            @if($role->permissions->contains('name', 'users_' . $action)) checked @endif>
                                        <label class="form-check-label" for="access-admin-checkbox-users_{{ $action }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            <!-- END Users Prermissions --> 

                            <!-- Roles Prermissions -->
                            <h2 class="content-heading pt-0 mt-5">Роли</h2>
                            @php
                                $rolePermissions = [
                                    'viewAny' => 'Просмотр всех ролей',
                                    'create' => 'Создание роли',
                                    'update' => 'Редактирование роли',
                                    'delete' => 'Удаление роли'
                                ];
                            @endphp
                            
                            @foreach($rolePermissions as $action => $label)
                                <div class="row g-sm push push">
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            value="1" 
                                            id="access-admin-checkbox-roles_{{ $action }}" 
                                            name="roles_{{ $action }}"
                                            @if($role->permissions->contains('name', 'roles_' . $action)) checked @endif>
                                        <label class="form-check-label" for="access-admin-checkbox-roles_{{ $action }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            <!-- END Roles Prermissions -->

                            <!-- Modules Permissions -->
                            @if(isset($allModulesData) && !empty($allModulesData))
                                @foreach ($allModulesData as $modules)
                                    @foreach ($modules as $module)
                                        <h2 class="content-heading pt-0 mt-5">{{ $module->name }}</h2>

                                        @php
                                            $modulePermissions = [
                                                'viewAny' => 'Просмотр всех записей',
                                                'view' => 'Просмотр записи',
                                                'create' => 'Создание записей', 
                                                'update' => 'Редактирование записей',
                                                'delete' => 'Удаление записей'
                                            ];
                                        @endphp

                                        @foreach($modulePermissions as $action => $label)
                                            <div class="row g-sm push push">
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        value="1" 
                                                        id="access-admin-checkbox-{{ $module->code }}_{{ $action }}" 
                                                        name="module_{{ $module->code }}_{{ $action }}"
                                                        @if($role->permissions->contains('name', 'module_' . $module->code . '_' . $action)) checked @endif>
                                                    <label class="form-check-label" for="access-admin-checkbox-{{ $module->code }}_{{ $action }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endif
                            <!-- END Modules Permissions -->
                        </div>
                    </div>
                </div>
                <!-- END Prermissions -->
                    
                <button class="btn btn-alt-success me-1 mb-3">
                    <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Сохранить
                </button>

                </div>

            </div>
        </form>
    
    </div>
    <!-- END Page Content -->

@endsection
