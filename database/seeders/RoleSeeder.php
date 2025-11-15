<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(['id' => 1], ['name' => 'Super Usuario']);
        DB::table('roles')->updateOrInsert(['id' => 2], ['name' => 'Administrador']);
        DB::table('roles')->updateOrInsert(['id' => 3], ['name' => 'Residente']);
    }
}