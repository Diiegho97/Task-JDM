@extends('layouts.app')

@section('content')
<x-app-layout>
     <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del usuario
            <span class="text-info ms-2">{{ $user->name }}</span>
        </h4>
    <hr>
    </hr>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Información del usuario</h4>
                    <dl class="row">
                        <dt class="col-sm-4">Nombre</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>
                        <dt class="col-sm-4">Correo electrónico</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>
                        <dt class="col-sm-4">Rol</dt>
                        <dd class="col-sm-8">{{ ucfirst($user->role) }}</dd>
                        @if($user->role === 'empleado' && $user->jefe)
                        <dt class="col-sm-4">Jefe</dt>
                        <dd class="col-sm-8">{{ $user->jefe->name }}</dd>
                        @endif
                        <dt class="col-sm-4">Creado</dt>
                        <dd class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                    <div class="d-flex justify-content-end">
                        @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        @endcan
                        @can('delete', $user)
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-user-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger me-2">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                        @endcan
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@endsection
