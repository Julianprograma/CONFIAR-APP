// database/seeders/InitialSetupSeeder.php
use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialSetupSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear Apartamento de Prueba
        $apartment = Apartment::create([
            'code' => 'P-000', 
            'area_sqm' => 100.00
        ]);
        
        // 2. Crear Super Usuario (role_id = 1)
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@condo.co',
            'password' => Hash::make('password'), // ¡Cámbiala después!
            'role_id' => 1, 
            'apartment_id' => $apartment->id
        ]);
    }
}