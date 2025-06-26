@extends('layouts.app')

@section('content')
<x-app-layout>
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Tarea
            <span class="text-warning ms-2">{{ $task->title }}</span>
        </h4>
<hr>

    <div class="card mx-auto shadow-lg rounded-4" style="max-width: 600px; border-radius: 0.5rem !important;">
        <div class="card-body">
            <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="title" class="form-label">Título *</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="due_date" class="form-label">Fecha de Vencimiento *</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Prioridad *</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="media" {{ old('priority', $task->priority) == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('priority', $task->priority) == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="baja" {{ old('priority', $task->priority) == 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="user_id" class="form-label">Asignar a usuario</label>
                    <select class="form-select" id="user_id" name="user_id">
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('user_id', $task->user_id) == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }} ({{ $usuario->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="completed" class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="completed" name="completed" value="1" {{ old('completed', $task->completed) ? 'checked' : '' }}>
                        Tarea completada
                    </label>
                </div>
                
                <div class="mb-3">
                    <label for="file" class="form-label">Archivo Adjunto</label>
                    @if($task->file_path)
                        <div class="mb-2">
                            <a href="{{ Storage::url($task->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-alt me-1"></i> Ver Archivo Actual
                            </a>
                        </div>
                    @endif
                    <input class="form-control" type="file" id="file" name="file">
                    <div class="form-text">Tamaño máximo: 2MB</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                   <form class="delete-task-form d-inline" action="{{ route('tasks.destroy', $task) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger me-2">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </form>
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-bookmark"></i> Actualizar
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
