use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Super Usuario'],
            ['id' => 2, 'name' => 'Administrador'],
            ['id' => 3, 'name' => 'Residente'],
        ]);
    }
}