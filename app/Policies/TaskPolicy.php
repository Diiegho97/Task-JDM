<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function view(User $user, Task $task)
    {
        // Admin puede ver cualquier tarea
        if ($user->role === 'admin') {
            return true;
        }
        return $user->id === $task->user_id;
    }

    public function update(User $user, Task $task)
    {
        // Admin puede actualizar cualquier tarea
        if ($user->role === 'admin') {
            return true;
        }
        // Jefe solo puede actualizar tareas de sus empleados directos o propias (no de otros jefes ni admins)
        if ($user->role === 'jefe') {
            return $task->user && $task->user->role === 'empleado' && ($task->user->jefe_id === $user->id || $task->user_id === $user->id);
        }
        // Empleado solo puede actualizar sus propias tareas
        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task)
    {
        // Admin puede eliminar cualquier tarea
        if ($user->role === 'admin') {
            return true;
        }
        // Jefe solo puede eliminar tareas de sus empleados directos o propias (no de otros jefes ni admins)
        if ($user->role === 'jefe') {
            return $task->user && $task->user->role === 'empleado' && ($task->user->jefe_id === $user->id || $task->user_id === $user->id);
        }
        // Empleado no puede eliminar tareas
        return false;
    }
}
