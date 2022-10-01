<?php

namespace App\Utils;

use DB;

use App\Contact;
use App\Customer;
use App\CustomerGroup;

class ContactUtil
{

    /**
     * Returns Walk In Customer for a Business
     *
     * @param int $business_id
     *
     * @return array/false
     */
    public function getWalkInCustomer($business_id)
    {
        $contact = Contact::leftJoin('tax_rate_tax_group AS trtg', 'contacts.tax_group_id', 'trtg.tax_group_id')
            ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
            ->where('contacts.type', 'customer')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.is_default', 1)
            ->select(
                'contacts.id',
                'contacts.name',
                'contacts.payment_condition',
                'contacts.pay_term_number',
                'contacts.pay_term_type',
                'contacts.tax_group_id',
                'tr.percent as tax_percent',
                'tr.min_amount',
                'tr.max_amount'
            )->first()
            ->toArray();

        if (!empty($contact)) {
            return $contact;
        } else {
            return false;
        }
    }

    /**
     * Returns Walk In Customer for a Business
     *
     * @param int $business_id
     *
     * @return array/false
     */
    public function getDefaultCustomer($business_id)
    {
        $contact = Customer::leftJoin('tax_rate_tax_group AS trtg', 'customers.tax_group_id', 'trtg.tax_group_id')
            ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
            ->where('customers.business_id', $business_id)
            ->where('customers.is_default', 1)
            ->select(
                'customers.id',
                'customers.name',
                'customers.tax_group_id',
                'customers.allowed_credit',
                'customers.is_withholding_agent',
                'customers.is_exempt',
                'tr.percent as tax_percent',
                'tr.min_amount',
                'tr.max_amount'
            )->first()
            ->toArray();

        if (!empty($contact)) {
            return $contact;
        } else {
            return false;
        }
    }

    /**
     * Returns the customer group
     *
     * @param int $business_id
     * @param int $customer_id
     *
     * @return array
     */
    public function getCustomerGroup($business_id, $customer_id)
    {
        $cg = [];

        if (empty($customer_id)) {
            return $cg;
        }

        $customer = Customer::leftjoin('customer_groups as CG', 'customers.customer_group_id', 'CG.id')
            ->where('customers.id', $customer_id)
            ->where('customers.business_id', $business_id)
            ->select('CG.*')
            ->first();

        return $customer;
    }

    /**
     * Get customer's employee asigned name
     * @param int $customer_id
     * @return string
     */
    public function getCustomerEmployeeName($customer_id){
        if(!$customer_id) return "";

        $employee_name = "";

        $customer_employeeName = Customer::
            join("customer_portfolios as cp", "customers.customer_portfolio_id", "cp.id")
            ->join("employees as e", "cp.seller_id", "e.id")
            ->where("customers.id", $customer_id)
            ->select(DB::raw("CONCAT(e.first_name, ' ', e.last_name) as name"))
            ->first();
        
        if(!empty($customer_employeeName)){
            $employee_name = $customer_employeeName->name;
        }

        return $employee_name;
    }

    /**
     * Get information taxes from contacts
     */
    public function getTaxInfo($contact_id){

        if(empty($contact_id)) { return null; }

        $contact = Contact::leftJoin('tax_rate_tax_group AS trtg', 'contacts.tax_group_id', 'trtg.tax_group_id')
            ->leftJoin('tax_rates as tr', 'trtg.tax_rate_id', 'tr.id')
            ->where('contacts.id', $contact_id)
            ->select(
                'contacts.tax_group_id',
                'tr.percent as tax_percent',
                'tr.min_amount',
                'tr.max_amount'
            )->first();
        
        return !empty($contact) ? $contact : null;
    }
}
