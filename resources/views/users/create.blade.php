@extends('layouts.app')

@section('content')
<x-app-layout>
    
        <h4 class="font-semibold text-xl text-gray-800 leading-tight font-center">
            Crear Usuario
        </h4>
    <hr>
    </hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico *</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Rol *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="jefe" {{ old('role') == 'jefe' ? 'selected' : '' }}>Jefe</option>
                                <option value="empleado" {{ old('role', 'empleado') == 'empleado' ? 'selected' : '' }}>Empleado</option>
                            </select>
                        </div>
                        <div class="mb-3" id="jefe-select-container" style="display: none;">
                            <label for="jefe_id" class="form-label">Jefe (solo para empleados y jefes)</label>
                            <select class="form-select" id="jefe_id" name="jefe_id">
                                <option value="">Sin jefe</option>
                                @foreach($jefes as $jefe)
                                    <option value="{{ $jefe->id }}" {{ old('jefe_id') == $jefe->id ? 'selected' : '' }}>{{ $jefe->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-user-plus"></i> Crear Usuario
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">
                                <i class="fa fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@endsection

@push('scripts') 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const jefeContainer = document.getElementById('jefe-select-container');
        function toggleJefe() {
            jefeContainer.style.display = (roleSelect.value === 'empleado' || roleSelect.value === 'jefe') ? 'block' : 'none';
        }
        roleSelect.addEventListener('change', toggleJefe);
        toggleJefe();
    });
</script>
@endpush