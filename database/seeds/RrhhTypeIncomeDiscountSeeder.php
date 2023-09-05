<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Business;
use App\RrhhTypeIncomeDiscount;

class RrhhTypeIncomeDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $business = Business::all();

        foreach ($business as $item) {
            RrhhTypeIncomeDiscount::firstOrCreate([
                'type' => 1, 
                'name' => 'Anticipo de sueldo', 
                'payroll_column' => 'Otras deducciones', 
                'status' => 1, 
                'business_id' => $item->id,
            ]);

            RrhhTypeIncomeDiscount::firstOrCreate([
                'type' => 2, 
                'name' => 'Anticipo de sueldo', 
                'payroll_column' => 'Otras deducciones', 
                'status' => 1, 
                'business_id' => $item->id,
            ]);

            RrhhTypeIncomeDiscount::firstOrCreate([
                'type' => 1, 
                'name' => 'Horas extras diurnas', 
                'payroll_column' => 'Número de horas extras diurnas', 
                'status' => 1, 
                'business_id' => $item->id,
            ]);

            RrhhTypeIncomeDiscount::firstOrCreate([
                'type' => 1, 
                'name' => 'Horas extras nocturnas', 
                'payroll_column' => 'Número de horas extras nocturnas', 
                'status' => 1, 
                'business_id' => $item->id,
            ]);

            RrhhTypeIncomeDiscount::firstOrCreate([
                'type' => 1, 
                'name' => 'Comisiones', 
                'payroll_column' => 'Comisiones', 
                'status' => 1, 
                'business_id' => $item->id,
            ]);

            RrhhTypeIncomeDiscount::firstOrCreate([
                'type' => 1, 
                'name' => 'Aguinaldo', 
                'payroll_column' => 'Aguinaldo', 
                'status' => 1, 
                'business_id' => $item->id,
            ]);

            RrhhTypeIncomeDiscount::firstOrCreate([
                'type' => 1, 
                'name' => 'Vacaciones', 
                'payroll_column' => 'Vacaciones', 
                'status' => 1, 
                'business_id' => $item->id,
            ]);
        }
    }
}
