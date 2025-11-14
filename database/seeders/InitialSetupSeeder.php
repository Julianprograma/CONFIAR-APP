<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Apartment; // Necesario para crear un apto de prueba

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Roles (Si no existen)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Usuario']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $residentRole = Role::firstOrCreate(['name' => 'Residente']);

        // 2. Crear Usuarios de Prueba (Si no existen)
        $usersToCreate = [
            [
                'role_id' => $superAdminRole->id,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'super@conjunto.com',
                'password' => 'password',
                'is_active' => true,
                'apartment_number' => null // No es residente
            ],
            [
                'role_id' => $adminRole->id,
                'first_name' => 'Gestor',
                'last_name' => 'Administrativo',
                'email' => 'admin@conjunto.com',
                'password' => 'password',
                'is_active' => true,
                'apartment_number' => null // No es residente
            ],
            [
                'role_id' => $residentRole->id,
                'first_name' => 'Juan',
                'last_name' => 'Perez',
                'email' => 'residente@apto101.com',
                'password' => 'password',
                'is_active' => true,
                'apartment_number' => '101' // Es residente
            ],
        ];
        
        foreach ($usersToCreate as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                
                $user = User::create([
                    'role_id' => $userData['role_id'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'is_active' => $userData['is_active'],
                ]);
                
                // 3. Asignar Apartamento si es Residente
                if ($userData['apartment_number']) {
                    Apartment::firstOrCreate(
                        ['apartment_number' => $userData['apartment_number']],
                        ['owner_id' => $user->id, 'square_meters' => 85.00]
                    );
                }
            }
        }
    }
}