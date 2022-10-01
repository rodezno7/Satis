<?php

use App\HumanResourceBanks;
use Illuminate\Database\Seeder;

class HumanResourceBanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Agricola, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Cuscatlán, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Davivienda Salvadoreño, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Hipotecario de El Salvador, S.A'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Citibank, N.A., Sucursal El Salvador'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco de Fomento Agropecuario'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco G&T Continental El Salvador, S.A'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Promerica, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco de America Central, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco ABANK, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Industrial El Salvador, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Atlántida El Salvador, S.A.'
        ]);
        HumanResourceBanks::firstOrCreate([
            'name' => 'Banco Azul de El Salvador, S.A.'
        ]);
    }
}
