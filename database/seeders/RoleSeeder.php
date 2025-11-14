<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; 

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nombres de roles según la lógica definida
        $roles = [
            'Super Usuario',
            'Administrador',
            'Residente',
        ];

        foreach ($roles as $roleName) {
            // Usa updateOrCreate para evitar duplicados si el seeder se ejecuta más de una vez
            Role::updateOrCreate(
                ['name' => $roleName],
                ['name' => $roleName]
            );
        }
    }
}