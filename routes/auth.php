<?php
// Archivo de rutas de autenticación (puedes agregar rutas aquí si usas Laravel Breeze, Jetstream, Fortify, etc.)

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
// Puedes agregar aquí las rutas de registro si lo necesitas
// Route::post('register', [RegisterController::class, 'register'])->name('register');
