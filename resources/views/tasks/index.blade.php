@extends('layouts.app')

@section('content')
<x-app-layout>
         <h4 class="font-semibold text-xl text-gray-800 leading-tight">
           Lista de mis tareas
           <span class="text-primary ms-2">{{ auth()->user()->name }}</span>
           <span class="text-muted ms-2">({{ ucfirst(auth()->user()->role) }})</span>
        </h4>
    <hr>
    </hr>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('tasks.index') }}" method="GET" class="row g-2">
                <div class="col-md-4">
                    <select name="priority" class="form-select" onchange="this.form.submit()">
                        <option value="">Todas las prioridades</option>
                        <option value="alta" {{ request('priority') == 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="media" {{ request('priority') == 'media' ? 'selected' : '' }}>Media</option>
                        <option value="baja" {{ request('priority') == 'baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'jefe')
                <div class="col-md-4">
                    <select name="user_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los responsables</option>
                        @php
                            $responsables = $tasks->pluck('user')->filter()->unique('id');
                        @endphp
                        @foreach($responsables as $responsable)
                            <option value="{{ $responsable->id }}" {{ request('user_id') == $responsable->id ? 'selected' : '' }}>
                                {{ $responsable->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar tareas..." value="{{ request('search') }}">
                        <button class="btn btn-secondary" type="submit">Buscar</button>
                    </div>
                </div>
            </form>
        </div><br><br>
        <hr>
        <div class="col-md-12 text-end">
            <form action="{{ route('tasks.report') }}" method="POST" class="d-inline-block me-2">
                @csrf
                <div class="input-group">
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}" required>
                    <span class="input-group-text">a</span>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}" required>
                    <button type="submit" class="btn btn-success ms-2">
                        <i class="fas fa-file-excel"></i> Descargar Excel
                    </button>
                </div>
            </form>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Tarea
            </a>
        </div>
    </div>

  <div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="tasks-table" class="table table-striped table-hover table-bordered w-100">
                <thead class="table-light">
                    <tr>
                        <th>Título</th>
                        <th>Prioridad</th>
                        <th>Fecha Vencimiento</th>
                        <th>Responsable</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr class="{{ $task->completed ? 'table-success' : '' }}">
                            <td>{{ $task->title }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $task->priority == 'alta' ? 'danger' : 
                                    ($task->priority == 'media' ? 'warning' : 'info') 
                                }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td data-order="{{ $task->due_date->format('Y-m-d') }}">
                                {{ $task->due_date->format('d/m/Y') }}
                            </td>
                            <td>{{ $task->user->name ?? '-' }}</td>
                            <td>
                                <form action="{{ route('tasks.toggle-complete', $task) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm w-100 {{ $task->completed ? 'btn-success' : 'btn-outline-secondary' }}">
                                        {{ $task->completed ? 'Completada' : 'Pendiente' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('update', $task)
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $task)
                                    <button type="button" class="btn btn-danger" title="Eliminar" onclick="if(confirm('¿Estás seguro de eliminar esta tarea?')) { this.nextElementSibling.submit(); }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="delete-form d-inline" style="display:none;">
                                        @csrf
                                        @method('DELETE')
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

@push('scripts')
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.6/r-2.5.0/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"></script>
    <script>
        $(document).ready(function() {
            $('#tasks-table').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // Título
                    { responsivePriority: 2, targets: -1 }, // Acciones
                    { orderable: false, targets: -1 }, // Deshabilitar ordenación en columna de acciones
                    { 
                        targets: 2, // Columna de fecha
                        type: 'date-eu' // Tipo de ordenación para fechas en formato europeo
                    }
                ],
                order: [[2, 'asc']] // Ordenar por fecha de vencimiento por defecto
            });
        });
    </script>
@endpush
    <style>
        #tasks-table tbody tr:hover {
            background-color: #f0f6ff !important;
            transition: background 0.2s;
        }
        #tasks-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
    </style>
    {{-- Eliminada la paginación de Laravel para evitar doble paginación con DataTables --}}
    {{-- <div class="d-flex justify-content-center">
        {{ $tasks->links() }}
    </div> --}}
</x-app-layout>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#tasks-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });
        document.querySelectorAll('.delete-form').forEach(function(form) {
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
