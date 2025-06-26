@extends('layouts.app')

@section('content')
<x-app-layout>
    
        <h4 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la tarea:
            <span class="text-info ms-2">{{ $task->title }}</span>
        </h4>
    <hr>
    </hr>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card mx-auto shadow-lg rounded-4" style="max-width: 600px; border-radius: 0.5rem !important;">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">{{ $task->title }}</h3>
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <p><strong>Prioridad:</strong>
                                    <span class="badge bg-{{ $task->priority == 'alta' ? 'danger' : ($task->priority == 'media' ? 'warning' : 'info') }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </p>
                                <p><strong>Fecha de Vencimiento:</strong> {{ $task->due_date->format('d/m/Y') }}</p>
                                <p><strong>Estado:</strong>
                                    <span class="badge bg-{{ $task->completed ? 'success' : 'secondary' }}">
                                        {{ $task->completed ? 'Completada' : 'Pendiente' }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-12 col-md-6">
                                @if($task->file_path)
                                    <p><strong>Archivo Adjunto:</strong></p>
                                    <a href="{{ Storage::url($task->file_path) }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                        <i class="fas fa-file-alt me-1"></i> Ver Archivo
                                    </a>
                                @else
                                    <p><strong>Archivo Adjunto:</strong> No hay archivo adjunto</p>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <h5>Descripción:</h5>
                            <p class="card-text">{{ $task->description ?? 'No hay descripción' }}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted">Creada el {{ $task->created_at->format('d/m/Y H:i') }}</small>
                            <div>
                                @can('update', $task)
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                @endcan
                                @can('delete', $task)
                                <form class="delete-task-form d-inline" action="{{ route('tasks.destroy', $task) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger me-2">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>
                                @endcan
                                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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