<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountBusinessLocation extends Model
{
    //
    protected $fillable = [
        'location_id',
        'general_cash_id',
        'inventory_account_id',
        'account_receivable_id',
        'vat_final_customer_id',
        'vat_taxpayer_id',
        'supplier_account_id',
        'provider_account_id',
        'sale_cost_id',
        'sale_expense_id',
        'admin_expense_id',
        'financial_expense_id',
        'local_sale_id',
        'exports_id'
    ];
}
