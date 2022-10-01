<?php

namespace App\Imports;

use App\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $business_id = null;
    protected $user_id = null;
    private $row_no = 2;

    public function __construct($business_id, $user_id) {
        $this->business_id = $business_id;
        $this->user_id = $user_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Customer([
            'name' => $row['name'],
            'business_name' => $row['business_name'],
            'email' => $row['email'],
            'telphone' => $row['phone'],
            'dni' => $row['dui'],
            'is_taxpayer' => $row['is_taxpayer'],
            'reg_number' => $row['reg_number'],
            'tax_number' => $row['tax_number'],
            'business_line' => $row['business_line'],
            'business_type_id' => $row['business_type'],
            'customer_portfolio_id' => $row['customer_portfolio'],
            'customer_group_id' => $row['customer_group'],
            'address' => $row['address'],
            'country_id' => $row['country'],
            'state_id' => $row['state'],
            'city_id' => $row['city'],
            'allowed_credit' => $row['allowed_credit'],
            'opening_balance' => $row['opening_balance'],
            'credit_limit' => $row['credit_limit'],
            'payment_term_id' => $row['payment_terms'],
            'opening_balance' => 0.00,
            'credit_limit' => 0.00,
            'credit_balance' => 0.00,
            'business_id' => $this->business_id,
            'created_by' => $this->user_id,
        ]);
    }

    /*public function rules(): array
    {
        return [
            'email' => function($attribute, $value, $onFailure) {
                if (!empty(trim($value))) {
                    if (!filter_var(trim($value), FILTER_VALIDATE_EMAIL)) {
                        $onFailure('Error en correo');
                    }
                }
            }
        ];
    }*/
}
