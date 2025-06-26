<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rutas de autenticación
require __DIR__.'/auth.php';

// Rutas públicas
Route::get('/', function () {
    return redirect()->route('tasks.index');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // CRUD de tareas
    Route::resource('tasks', TaskController::class);
    // Marcar tarea como completada/pendiente
    Route::patch('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])
        ->name('tasks.toggle-complete');
    // CRUD de usuarios
    Route::resource('users', UserController::class);
    // Reporte de tareas a Excel (descarga por POST, vista por GET)
    Route::get('tasks/report', [TaskController::class, 'report'])->name('tasks.report');
    Route::post('tasks/report', [TaskController::class, 'report']);
});

// Rutas de autenticación manuales (login, logout, etc.) ya están en routes/auth.php