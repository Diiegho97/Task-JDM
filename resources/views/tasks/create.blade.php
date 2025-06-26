@extends('layouts.app')

@section('content')
<x-app-layout>
    {{-- <x-slot name="header"> --}}
        <div class="d-flex justify-content-center mb-4">
            <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nueva Tarea
            </h4>
        </div>
    {{-- </x-slot> --}}

    <div class="card mx-auto shadow-lg rounded-4" style="max-width: 600px; border-radius: 0.5rem !important;">
        <div class="card-body">
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="title" class="form-label">Título *</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="due_date" class="form-label">Fecha de Vencimiento *</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Prioridad *</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="media" {{ old('priority') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="baja" {{ old('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>
                </div>
                
                @php $user = Auth::user(); @endphp
                @if($user->role === 'admin' || $user->role === 'jefe')
                <div class="mb-3">
                    <label for="user_id" class="form-label">Asignar a usuario *</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Seleccione un usuario</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}" {{ old('user_id', $user->id) == $empleado->id ? 'selected' : '' }}>
                                {{ $empleado->name }} ({{ $empleado->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                @endif
                
                <div class="mb-3">
                    <label for="file" class="form-label">Archivo Adjunto</label>
                    <input class="form-control" type="file" id="file" name="file">
                    <div class="form-text">Tamaño máximo: 2MB</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fa fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#tasks-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                paging: false,
                info: false,
                searching: false
            });
        });
        document.querySelectorAll('.delete-task-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción eliminará la tarea de forma permanente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush