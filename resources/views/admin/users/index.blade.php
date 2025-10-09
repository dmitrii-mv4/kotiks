@extends('admin.layouts.default')

@section('content')
    <!-- Hero -->
    <div class="content">
        <div
            class="d-md-flex justify-content-md-between align-items-md-center py-3 pt-md-3 pb-md-0 text-center text-md-start">
            <div>
                <h1 class="h3 mb-1">Пользователи</h1>
            </div>
            <div class="mt-4 mt-md-0">
                <nav class="flex-shrink-0 my-2 my-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Пользователи</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">

        @can('create', \App\Models\User::class)
        <a href="{{ route('admin.users.create') }}">
            <button type="button" class="btn btn-alt-success me-1 mb-3">
                <i class="fa fa-fw fa-plus opacity-50 me-1"></i> Добавить пользователя
            </button>
        </a>
        @endcan

        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Список пользователей</h3>
            </div>
            <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 100px;">
                                    <i class="far fa-user"></i>
                                </th>
                                <th>Имя</th>
                                <th style="width: 30%;">Email</th>
                                <th style="width: 15%;">Роль</th>
                                <th class="text-center" style="width: 100px;">Опции</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-center">
                                        <img class="img-avatar img-avatar48" src="{{ $user->avatar }}"
                                            alt="">
                                    </td>
                                    <td class="fw-semibold">
                                        <a href="{{ route('admin.users') }}">{{ $user->name }}</a>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role->name ?? 'Не указана' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">

                                            @can('update', $user)
                                            <a href="{{ route('admin.users.edit', $user->id) }}" type="button"
                                                class="btn btn-sm btn-alt-secondary js-bs-tooltip-enabled"
                                                data-bs-toggle="tooltip" aria-label="Edit" data-bs-original-title="Edit">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                            @endcan

                                            @can('delete', $user)
                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <input type="hidden" name="user_id" value="{{ $user->id }}">

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
            </div>
        </div>

    </div>
    <!-- END Page Content -->
@endsection
