@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Создание меню</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">Интерфейс</li>
                        <li class="breadcrumb-item" aria-current="page">Настройки меню</li>
                        <li class="breadcrumb-item active" aria-current="page">Создание меню</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        <form action="{{ route('admin.interface.menu.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="block block-rounded">
                <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" id="role-tab" data-bs-toggle="tab" data-bs-target="#role-classic" role="tab" aria-controls="role-classic" aria-selected="true">
                        Меню
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" id="prermissions-tab" data-bs-toggle="tab" data-bs-target="#prermissions" role="tab" aria-controls="search-photos" aria-selected="false" tabindex="-1">
                        Пункты
                        </button>
                    </li>
                </ul>
                <div class="block-content tab-content overflow-hidden">

                    <!-- Menu -->
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
                                                <input type="text" class="@error('name') is-invalid @enderror form-control" id="example-text-input" name="name" placeholder="" value="{{ old('name') }}">
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
                    <!-- END Menu -->

                    <!-- Points -->
                    <div class="tab-pane fade" id="prermissions" role="tabpanel" aria-labelledby="prermissions-tab" tabindex="0">
                        <div class="row g-sm push">
                        
                            <div class="block-content">

                                <div class="text-center">
                                    <button type="button" id="addItemBtn" class="btn btn-primary add-item-btn">
                                        <i class="fas fa-plus-circle"></i> Добавить пункт меню
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-vcenter">
                                        <thead>
                                            <tr>
                                                <th>Название</th>
                                                <th class="text-center">Ссылка</th>
                                                <th width="10%" class="text-center">Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody id="menuItems">
                                            <tr class="menu-item-row">
                                                <td class="fw-semibold">  
                                                    <input type="text" class="@error('items.0.title') is-invalid @enderror form-control" id="example-text-input" name="items[0][title]" placeholder="Введите название" value="{{ old('items.0.title') }}">
                                                    @error('items.0.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" style="width: 100%">
                                                        <input type="text" class="@error('items[0][url]') is-invalid @enderror form-control" id="example-text-input" name="items[0][url]" placeholder="Введите URL" value="{{ old('items[0][url]') }}">
                                                        @error('items[0][url]')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn btn-sm btn-danger remove-item-btn disabled" disabled>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
