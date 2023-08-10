<?php

use App\Business;
use App\RrhhTypeWage;
use Illuminate\Database\Seeder;

class RrhhTypeWageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business = Business::all();
        foreach($business as $item){
            RrhhTypeWage::firstOrCreate([
                'name' => 'Asalariado',
                'isss' => 1,
                'afp' => 1,
                'type' => 'Ley de salario',
                'business_id' => $item->id,
                'deleted_at' => null
            ]);

            RrhhTypeWage::firstOrCreate([
                'name' => 'Servicio profesional',
                'isss' => 0,
                'afp' => 0,
                'type' => 'Honorario',
                'business_id' => $item->id,
                'deleted_at' => null
            ]);
        }
    }
}
