<?php

use App\ExpenseLine;
use App\Transaction;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Database\Seeder;

class ModifyExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $current_expenses = Transaction::where('type', 'expense')
            ->select('id', 'expense_category_id', 'total_before_tax as line_total')
            ->get();

        foreach ($current_expenses as $ce) {
            ExpenseLine::create([
                'transaction_id' => $ce->id,
                'expense_category_id' => $ce->expense_category_id,
                'line_total_exc_tax' => $ce->line_total
            ]);
        }
    }
}
