<?php

use Illuminate\Database\Seeder;

class TransferStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            \DB::beginTransaction();
    
            # Clean table
            \DB::table('transfer_states')->delete();
    
            # Insert data
            \DB::table('transfer_states')->insert([
                0 => ['id' => 1, 'name' => 'created', 'created_at' => \Carbon::now(), 'updated_at' => \Carbon::now()],
                1 => ['id' => 2, 'name' => 'processed', 'created_at' => \Carbon::now(), 'updated_at' => \Carbon::now()],
                2 => ['id' => 3, 'name' => 'received', 'created_at' => \Carbon::now(), 'updated_at' => \Carbon::now()],
                3 => ['id' => 4, 'name' => 'accounted', 'created_at' => \Carbon::now(), 'updated_at' => \Carbon::now()],
            ]);

            \DB::commit();

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        }
    }
}
