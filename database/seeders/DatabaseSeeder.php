<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear Admin principal
        $admin = User::factory()->create([
            'name' => 'Administrador Principal',
            'email' => 'admin@plataforma.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'jefe_id' => null,
        ]);

        // Crear Jefes y asignarles el admin como jefe
        $jefes = [
            [
                'name' => 'Jefe Juan',
                'email' => 'jefe.juan@plataforma.com',
            ],
            [
                'name' => 'Jefe Ana',
                'email' => 'jefe.ana@plataforma.com',
            ],
        ];
        $jefeUsers = [];
        foreach ($jefes as $jefeData) {
            $jefeUsers[] = User::factory()->create([
                'name' => $jefeData['name'],
                'email' => $jefeData['email'],
                'role' => 'jefe',
                'password' => Hash::make('jefe123'),
                'jefe_id' => $admin->id,
            ]);
        }

        // Crear Empleados y asignarles un jefe
        $empleados = [
            [
                'name' => 'Empleado Pedro',
                'email' => 'empleado.pedro@plataforma.com',
                'jefe_id' => $jefeUsers[0]->id,
            ],
            [
                'name' => 'Empleado Maria',
                'email' => 'empleado.maria@plataforma.com',
                'jefe_id' => $jefeUsers[0]->id,
            ],
            [
                'name' => 'Empleado Luis',
                'email' => 'empleado.luis@plataforma.com',
                'jefe_id' => $jefeUsers[1]->id,
            ],
            [
                'name' => 'Empleado Sofia',
                'email' => 'empleado.sofia@plataforma.com',
                'jefe_id' => $jefeUsers[1]->id,
            ],
        ];
        foreach ($empleados as $empData) {
            User::factory()->create([
                'name' => $empData['name'],
                'email' => $empData['email'],
                'role' => 'empleado',
                'password' => Hash::make('empleado123'),
                'jefe_id' => $empData['jefe_id'],
            ]);
        }

        $this->call(TaskSeeder::class);
    }
}
