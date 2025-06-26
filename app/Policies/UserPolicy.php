<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        // Solo admin y jefe pueden crear usuarios
        return $user->role === 'admin' || $user->role === 'jefe';
    }

    public function update(User $user, User $model)
    {
        // Admin puede editar cualquier usuario
        if ($user->role === 'admin') {
            return true;
        }
        // Jefe solo puede editar a sus empleados (no a otros jefes ni a sí mismo ni a otros jefes)
        if ($user->role === 'jefe') {
            return $model->role === 'empleado' && $model->jefe_id === $user->id;
        }
        return false;
    }

    public function delete(User $user, User $model)
    {
        // Admin puede eliminar cualquier usuario
        if ($user->role === 'admin') {
            return true;
        }
        // Jefe solo puede eliminar a sus empleados directos
        if ($user->role === 'jefe') {
            return $model->role === 'empleado' && $model->jefe_id === $user->id;
        }
        return false;
    }
}