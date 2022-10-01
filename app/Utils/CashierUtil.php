<?php
namespace App\Utils;

use App\Cashier;
use App\CashierClosure;

class CashierUtil extends Util{
    /**
     * Returns number of opened cashiers for current logged user
     * @return int
     */
    public function countOpenedCashier(){
        $permitted_cashiers = Cashier::permittedCashiers();

        if ($permitted_cashiers != 'all') {
            $count =
            Cashier::whereIn('id', $permitted_cashiers)
                    ->where('status', 'open')
                    ->count();
        } else {
            $count =
            Cashier::where('status', 'open')
                    ->count();
        }
        return $count;        
    }

    /**
     * Return current cashier closure for a cashier open
     * @param int $cashier_id
     * @return int
     */
    public function getCashierClosureActive($cashier_id){
        $cashier_closure =
            CashierClosure::whereNull('closed_by')
                ->where('cashier_id', $cashier_id)
                ->whereNull('close_date')
                ->first();
                
        return $cashier_closure ? $cashier_closure->id : null;
    }

    /**
     * Returnss last cashier closured
     * @param int $cashier_id
     */
    public function getLastCashierClosure($cashier_id){
        $cashier_closure =
            CashierClosure::orderBy('close_date', 'desc')
                ->first();

        return $cashier_closure ? $cashier_closure->id : null;   
    }
}