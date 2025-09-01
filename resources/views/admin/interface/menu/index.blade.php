@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Настройки меню</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Интерфейс</li>
                        <li class="breadcrumb-item active" aria-current="page">Настройки меню</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        <a href="{{ route('admin.interface.menu.create') }}">
            <button type="button" class="btn btn-alt-success me-1 mb-3">
                <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Создать меню
            </button>
        </a>

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Список меню</h3>
            </div>
            <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 100px;">
                                    ID
                                </th>
                                <th>Название</th>
                                <th class="text-center" style="width: 100px;">Опции</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menus as $menu)
                                <tr>
                                    <td class="text-center">
                                        {{ $menu->id }}
                                    </td>
                                    <td class="fw-semibold">
                                        {{ $menu->name }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="" type="button"
                                                class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled"
                                                data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>

                                            <button type="button" class="btn btn-alt-primary push" data-bs-toggle="modal" data-bs-target="#modal-block-select2">Launch Modal</button>

                                            <!-- Select2 in a modal -->
                                            <div class="modal" id="modal-block-select2" tabindex="-1" role="dialog" aria-labelledby="modal-block-select2" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="block block-rounded block-transparent mb-0">
                                                    <div class="block-header block-header-default">
                                                        <h3 class="block-title">Редактирование меню</h3>
                                                        <div class="block-options">
                                                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="fa fa-fw fa-times"></i>
                                                        </button>
                                                        </div>
                                                    </div>
                                                    <div class="block-content">
                                                        <!-- Select2 is initialized at the bottom of the page -->
                                                        <form action="" method="POST" onsubmit="return false;">
                                                            <input type="text" class="@error('name') is-invalid @enderror form-control" id="example-text-input" name="name" placeholder="" value="{{ old('$menu->name') }}">
                                                                @error('name')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                        </form>
                                                    </div>
                                                    <div class="block-content block-content-full text-end bg-body">
                                                        <button type="button" class="btn btn-sm btn-alt-secondary me-1" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Perfect</button>
                                                    </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                            <!-- END Select2 in a modal -->

                                            <form action="" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                                                <button type="submit"
                                                    class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled"
                                                    data-bs-toggle="tooltip" aria-label="Delete"
                                                    data-bs-original-title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- END Page Content -->
@endsection
