<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los usuarios
        $admins = User::where('role', 'admin')->get();
        $jefes = User::where('role', 'jefe')->get();
        $empleados = User::where('role', 'empleado')->get();

        // Tareas para admin
        foreach ($admins as $admin) {
            for ($i = 1; $i <= 2; $i++) {
                Task::create([
                    'title' => 'Tarea Admin ' . $i,
                    'description' => 'Tarea creada por el admin',
                    'due_date' => now()->addDays($i),
                    'priority' => 'alta',
                    'completed' => false,
                    'file_path' => null,
                    'user_id' => $admin->id,
                ]);
            }
        }

        // Tareas para jefes
        foreach ($jefes as $jefe) {
            for ($i = 1; $i <= 2; $i++) {
                Task::create([
                    'title' => 'Tarea Jefe ' . $i,
                    'description' => 'Tarea creada por el jefe',
                    'due_date' => now()->addDays($i+2),
                    'priority' => 'media',
                    'completed' => false,
                    'file_path' => null,
                    'user_id' => $jefe->id,
                ]);
            }
        }

        // Tareas para empleados
        foreach ($empleados as $empleado) {
            for ($i = 1; $i <= 2; $i++) {
                Task::create([
                    'title' => 'Tarea Empleado ' . $i,
                    'description' => 'Tarea asignada al empleado',
                    'due_date' => now()->addDays($i+4),
                    'priority' => 'baja',
                    'completed' => false,
                    'file_path' => null,
                    'user_id' => $empleado->id,
                ]);
            }
        }
    }
}
