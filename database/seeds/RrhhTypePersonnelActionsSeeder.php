<?php

use App\RrhhTypePersonnelAction;
use App\Business;
use Illuminate\Database\Seeder;

class RrhhTypePersonnelActionsSeeder extends Seeder
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
            RrhhTypePersonnelAction::firstOrCreate([
                'name' => 'Cambio de salario', 
                'required_authorization' => 1, 
                'apply_to_many' => 1, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            RrhhTypePersonnelAction::firstOrCreate([
                'name' => 'Cambio de puesto',
                'required_authorization' => 1, 
                'apply_to_many' => 0, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            RrhhTypePersonnelAction::firstOrCreate([
                'name' => 'Reincorporación', 
                'required_authorization' => 0, 
                'apply_to_many' => 0, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            RrhhTypePersonnelAction::firstOrCreate([
                'name' => 'Despido justificado',
                'required_authorization' => 1, 
                'apply_to_many' => 0, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);
            RrhhTypePersonnelAction::firstOrCreate([
                'name' => 'Permiso', 
                'required_authorization' => 0, 
                'apply_to_many' => 0, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);

            RrhhTypePersonnelAction::firstOrCreate([
                'name' => 'Cambio de forma de pago',
                'required_authorization' => 0, 
                'apply_to_many' => 0, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);
            RrhhTypePersonnelAction::firstOrCreate([
                'name' => 'Cambio de cuenta bancaria', 
                'required_authorization' => 0, 
                'apply_to_many' => 0, 
                'business_id' => $item->id, 
                'deleted_at' => null
            ]);
        }
    }
}
