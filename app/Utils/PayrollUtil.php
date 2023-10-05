<?php

namespace App\Utils;

use App\LawDiscount;
use DB;

class PayrollUtil extends Util
{

    public function calculateIsss($total_income, $business_id, $isr_id)
    {
        $lawDiscounts = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
            ->join('payment_periods as payment_period', 'payment_period.id', '=', 'law_discounts.payment_period_id')
            ->select('law_discounts.id as id', 'law_discounts.*', 'payment_period.id as payment_period_id', 'institution_law.name as institution_law')
            ->where('payment_period.id', $isr_id)
            ->where('law_discounts.business_id', $business_id)
            ->where('law_discounts.deleted_at', null)
            ->get();

        $isss = 0;
        foreach ($lawDiscounts as $lawDiscount) {
            if (mb_strtolower($lawDiscount->institution_law) == mb_strtolower('ISSS')) {
                if ($lawDiscount->payment_period_id == $isr_id) {
                    if ($total_income >= $lawDiscount->until) {
                        $isss = $lawDiscount->until * $lawDiscount->employee_percentage / 100;
                    } else {
                        $isss = $total_income * $lawDiscount->employee_percentage / 100;
                    }
                }
            }
        }

        return $isss;
    }

    public function calculateAfp($total_income, $business_id, $isr_id)
    {
        $afp = 0;

        $lawDiscounts = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
            ->join('payment_periods as payment_period', 'payment_period.id', '=', 'law_discounts.payment_period_id')
            ->select('law_discounts.id as id', 'law_discounts.*', 'payment_period.id as payment_period_id', 'institution_law.name as institution_law')
            ->where('payment_period.id', $isr_id)
            ->where('law_discounts.business_id', $business_id)
            ->where('law_discounts.deleted_at', null)
            ->get();

        foreach ($lawDiscounts as $lawDiscount) {
            if (mb_strtolower($lawDiscount->institution_law) == mb_strtolower('AFP Confia') || mb_strtolower($lawDiscount->institution_law) == mb_strtolower('AFP Crecer')) {
                if ($lawDiscount->payment_period_id == $isr_id) {
                    $afp = $total_income * $lawDiscount->employee_percentage / 100;
                }
            }
        }

        return $afp;
    }


    public function calculateRent($total_income, $business_id, $isr_id, $isss, $afp){
        $lawDiscountsRenta = LawDiscount::join('institution_laws as institution_law', 'institution_law.id', '=', 'law_discounts.institution_law_id')
        ->join('payment_periods as payment_period', 'payment_period.id', '=', 'law_discounts.payment_period_id')
        ->select('law_discounts.id as id', 'law_discounts.*', 'institution_law.name as institution_law', 'payment_period.name as payment_period')
        ->where('institution_law.name', 'Renta')
        ->where('payment_period.id', $isr_id)
        ->where('law_discounts.business_id', $business_id)
        ->where('law_discounts.deleted_at', null)
        ->get();

        $rent = 0;
        for ($i = 0; $i < count($lawDiscountsRenta); $i++) {
            $value = $total_income - $isss - $afp;

            if ($value <= $lawDiscountsRenta[0]->until) {
                $rent = 0;
            } else {
                if ($value <= $lawDiscountsRenta[1]->until) {
                    $rent = bcdiv((($value - $lawDiscountsRenta[1]->base) * ($lawDiscountsRenta[1]->employee_percentage / 100)) + $lawDiscountsRenta[1]->fixed_fee, 1, 2);
                } else {
                    if ($value <= $lawDiscountsRenta[2]->until) {
                        $rent = bcdiv((($value - $lawDiscountsRenta[2]->base) * ($lawDiscountsRenta[2]->employee_percentage / 100)) + $lawDiscountsRenta[2]->fixed_fee, 1, 2);
                    } else {
                        if ($value <= $lawDiscountsRenta[3]->until) {
                            $rent = bcdiv((($value - $lawDiscountsRenta[3]->base) * ($lawDiscountsRenta[3]->employee_percentage / 100)) + $lawDiscountsRenta[3]->fixed_fee, 1, 2);
                        }
                    }
                }
            }
        }

        return $rent;
    }
}
