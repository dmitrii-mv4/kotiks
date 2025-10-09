@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Добавление пользователя</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Пользователи</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Добавление пользователя</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        <div class="block block-rounded">
            <div class="block-content">
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                    <!-- Basic Elements -->
                    <div class="row push">
                        <div class="col-lg-3">
                            <label class="form-label" for="example-text-input">Имя:</label>
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
                            <label class="form-label" for="example-text-input">Email:</label>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="mb-4">
                                <input type="text" class="@error('email') is-invalid @enderror form-control" id="example-text-input" name="email" placeholder="" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row push">
                        <div class="col-lg-3">
                            <label class="form-label" for="example-text-input">Роль:</label>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="mb-4">
                                <select class="form-select" id="example-select" name="role_id">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row push">
                        <div class="col-lg-3">
                            <label class="form-label" for="example-text-input">Пароль:</label>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="mb-4">
                                <input type="password" class="@error('password') is-invalid @enderror form-control" id="example-text-input" name="password" placeholder="">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row push">
                        <div class="col-lg-3">
                            <label class="form-label" for="example-text-input">Подтверждение пароля:</label>
                        </div>
                        <div class="col-lg-8 col-xl-5">
                            <div class="mb-4">
                                <input type="password" class="@error('password_confirmation') is-invalid @enderror form-control" id="example-text-input" name="password_confirmation" placeholder="">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-alt-success me-1 mb-3">
                        <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Добавить
                    </button>

                </form>
            </div>
        </div>

    </div>
    <!-- END Page Content -->
@endsection
