<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;



class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $user = Auth::user();
        $query = Task::query();

        // Filtro por rol
        if ($user->role === 'admin') {
            // No filtro por usuario
        } elseif ($user->role === 'jefe') {
            $empleados = User::where('jefe_id', $user->id)->pluck('id');
            $query->whereIn('user_id', $empleados->push($user->id));
        } else {
            // Empleado: solo ve tareas donde es responsable
            $query->where('user_id', $user->id);
        }

        // Filtro por prioridad
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%") ;
            });
        }

        // Filtro por responsable
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $tasks = $query->get(); // Mostrar todas las tareas sin paginación
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $empleados = User::whereIn('role', ['jefe', 'empleado'])->get();
        } elseif ($user->role === 'jefe') {
            // Solo empleados cuyo jefe_id es el id del jefe y el propio jefe
            $empleados = User::where('jefe_id', $user->id)
                ->orWhere('id', $user->id)
                ->get();
        } else {
            $empleados = User::where('id', $user->id)->get();
        }
        return view('tasks.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'required|date',
            'priority' => 'required|in:alta,media,baja',
            'file' => 'nullable|file|max:2048',
        ];
        if ($user->role === 'admin' || $user->role === 'jefe') {
            $rules['user_id'] = 'required|exists:users,id';
        }
        $validated = $request->validate($rules);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('task_files', 'public');
        }

        $task = new Task([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'file_path' => $filePath,
        ]);

        if ($user->role === 'admin' || $user->role === 'jefe') {
            $assignedUser = User::find($validated['user_id']);
        } else {
            $assignedUser = $user;
        }
        $assignedUser->tasks()->save($task);

        return redirect()->route('tasks.index')->with('success', 'Tarea creada exitosamente!');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $user = Auth::user();
        if ($user->role === 'admin') {
            $usuarios = User::whereIn('role', ['jefe', 'empleado'])->get();
        } elseif ($user->role === 'jefe') {
            $usuarios = User::where('jefe_id', $user->id)
                ->orWhere('id', $user->id)
                ->get();
        } else {
            $usuarios = User::where('id', $user->id)->get();
        }
        return view('tasks.edit', compact('task', 'usuarios'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $user = Auth::user();
        $rules = [
            'title' => 'required|max:255',
            'description' => 'nullable',
            'due_date' => 'required|date',
            'priority' => 'required|in:alta,media,baja',
            'completed' => 'boolean',
            'file' => 'nullable|file|max:2048',
        ];
        if ($user->role === 'admin' || $user->role === 'jefe') {
            $rules['user_id'] = 'required|exists:users,id';
        }
        $validated = $request->validate($rules);

        $filePath = $task->file_path;
        if ($request->hasFile('file')) {
            // Eliminar archivo anterior si existe
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('task_files', 'public');
        }

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'completed' => $validated['completed'] ?? false,
            'file_path' => $filePath,
        ];
        if ($user->role === 'admin' || $user->role === 'jefe') {
            $updateData['user_id'] = $validated['user_id'];
        }
        $task->update($updateData);

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        // Eliminar archivo asociado si existe
        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada exitosamente!');
    }

    public function toggleComplete(Task $task)
    {
        $this->authorize('update', $task);
        
        $task->update([
            'completed' => !$task->completed
        ]);

        return back()->with('success', 'Estado de la tarea actualizado!');
    }

    public function report(Request $request)
    {
        // Si es GET, mostrar la vista con la tabla
        if ($request->isMethod('get')) {
            $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from',
            ]);
            $from = $request->from;
            $to = $request->to;

            $tasks = Task::with('user')
                ->whereDate('due_date', '>=', $from)
                ->whereDate('due_date', '<=', $to)
                ->get();

            return view('tasks.report', compact('tasks', 'from', 'to'));
        }

        // Si es POST, descargar el CSV
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);
        $from = $request->from;
        $to = $request->to;

        $tasks = Task::with('user')
            ->whereDate('due_date', '>=', $from)
            ->whereDate('due_date', '<=', $to)
            ->get();

        $data = $tasks->map(function($task) {
            return [
                'ID' => $task->id,
                'Título' => $task->title,
                'Descripción' => $task->description,
                'Prioridad' => $task->priority,
                'Fecha Vencimiento' => $task->due_date ? $task->due_date->format('Y-m-d') : '',
                'Estado' => $task->completed ? 'Completada' : 'Pendiente',
                'Responsable' => $task->user ? $task->user->name : '-',
                'Email Responsable' => $task->user ? $task->user->email : '-',
                'Rol Responsable' => $task->user ? $task->user->role : '-',
            ];
        });

        $filename = 'reporte_tareas_' . $from . '_a_' . $to . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_keys($data->first() ?? []));
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        return Response::make($csv, 200, $headers);
    }
}