<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $users = User::with('jefe')->paginate(10);
        } elseif ($user->role === 'jefe') {
            // El jefe ve solo a sí mismo y a sus empleados (empleados a cargo)
            $users = User::with('jefe')
                ->where('id', $user->id)
                ->orWhere('jefe_id', $user->id)
                ->paginate(10);
        } else {
            // Empleado solo se ve a sí mismo
            $users = User::with('jefe')->where('id', $user->id)->paginate(10);
        }
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $jefes = \App\Models\User::where('role', 'jefe')->get();
        return view('users.create', compact('jefes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,jefe,empleado',
            'password' => 'required|string|min:6|confirmed',
            'jefe_id' => 'nullable|exists:users,id',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        // Solo asignar jefe_id si el rol no es admin
        if ($validated['role'] === 'admin') {
            $validated['jefe_id'] = null;
        }
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $jefes = \App\Models\User::where('role', 'jefe')->get();
        return view('users.edit', compact('user', 'jefes'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,jefe,empleado',
            'password' => 'nullable|string|min:6|confirmed',
            'jefe_id' => 'nullable|exists:users,id',
        ]);
        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        // Solo asignar jefe_id si el rol no es admin
        if ($validated['role'] === 'admin') {
            $validated['jefe_id'] = null;
        }
        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
