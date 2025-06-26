@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg rounded-4" style="border-radius: 0.5rem !important;">
                    <div class="card-body">

                        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
                            Editar Usario
                            <span class="text-warning ms-2">{{ $user->name }}</span>
                        </h4>
                        <hr>

                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña (dejar en blanco para no
                                    cambiar)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Rol *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Administrador</option>
                                    <option value="jefe" {{ old('role', $user->role) == 'jefe' ? 'selected' : '' }}>Jefe
                                    </option>
                                    <option value="empleado" {{ old('role', $user->role) == 'empleado' ? 'selected' : '' }}>
                                        Empleado</option>
                                </select>
                            </div>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'jefe')
                            <div class="mb-3" id="jefe-select-container" style="display: none;">
                                <label for="jefe_id" class="form-label">Jefe (solo para empleados y jefes)</label>
                                <select class="form-select" id="jefe_id" name="jefe_id">
                                    <option value="">Sin jefe</option>
                                    @foreach ($jefes as $jefe)
                                        <option value="{{ $jefe->id }}"
                                            {{ old('jefe_id', $user->jefe_id) == $jefe->id ? 'selected' : '' }}>
                                            {{ $jefe->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
