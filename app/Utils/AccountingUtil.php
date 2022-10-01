<?php
namespace App\Utils;

use DB;
use Carbon;
use App\Business;
use App\FiscalYear;
use App\TypeEntrie;
use App\BankTransaction;
use App\AccountingPeriod;
use App\AccountingEntrie;
use App\TypeBankTransaction;
use App\AccountingEntriesDetail;

Class AccountingUtil extends Util {
    /**
     * Create a new Accounting Entry
     * @param 
     * 
     */
    public function createAccountingEntry($entry, $entry_lines, $transaction_date){
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();
        $date = Carbon::parse($entry['date']);
        $number = $this->getEntryNumber($transaction_date);
        
        /** Validate fiscal year */
        $fiscal_year = FiscalYear::where("year", $date->year)->first();
        if(!empty($fiscal_year)) {
            
            /** Validate period */
            $period =
                AccountingPeriod::where("fiscal_year_id", $fiscal_year->id)
                    ->where("month", $date->month)
                    ->where("status", 1)
                    ->first();
            
            if(!empty($period)){
                $entry['accounting_period_id'] = $period->id;
                $entry['number'] = $number;

                if ($business->enable_validation_entries == 1) {
                    $entry['status'] = 0;
                    $entry['correlative'] = 0;
                }
                else {
                    $entry['status'] = 1;
                    $entry['correlative'] = $number;
                }

                /** Create accounting entry */
                $accounting_entry = AccountingEntrie::create($entry);

                /** Create accounting entry lines for products */
                foreach ($entry_lines as $el) {
                    if($el['amount'] > 0){
                        AccountingEntriesDetail::create([
                            'entrie_id' => $accounting_entry->id,
                            'account_id' => $el['catalogue_id'],
                            'debit' => $el['type'] == 'debit' ? $el['amount'] : 0,
                            'credit' => $el['type'] == 'credit' ? $el['amount'] : 0,
                            'description' => isset($el['description']) ? ($el['description'] ? $el['description'] : '') : '',
                        ]);
                    }
                }

                return [
                    'success' => true,
                    'msg' => __("accounting.accounting_entry_generated_success")
                ];

            } else {
                return [
                    'success' => false,
                    'msg' => __("accounting.period_invalid")
                ];
            }
        } else {
            return [
                'success' => false,
                'msg' => __("accounting.period_invalid")
            ];
        }
    }

    /**
     * Get accounting entry number
     * @param Date $date
     * @return int
     */
    public function getEntryNumber($date)
    {
        $business_id = request()->session()->get('user.business_id');
        $entry_date = Carbon::parse($date);
        $month = $entry_date->month;
        $year = $entry_date->year;

        $mode_numeration =
            Business::where('id', $business_id)
                ->value('entries_numeration_mode');

        if($mode_numeration == 'month'){
            $count = AccountingEntrie::where('status', 1)
                ->whereMonth('date', $month)
                ->max("number");
            if($count == null){
                $code = 1;
            }
            else {
                $code = $count + 1;
            }
        } else if($mode_numeration == 'year'){
            $count = AccountingEntrie::where('status', 1)
                ->whereYear('date', $year)
                ->max("number");
            if($count == null){
                $code = 1;
            }
            else {
                $code = $count + 1;
            }
        } else {
            $code = 0;
        }

        return $code;
    }

    /**
     * create bank transaction entry
     * @param Array $remittance_entry
     * @return boolean
     */
    public function createBankTransactionEntry($remittance_entry){
        if(empty($remittance_entry['bank_account_id']) ||
            empty($remittance_entry['accounting_entrie_id']) ||
            empty($remittance_entry['reference']) ||
            empty($remittance_entry['date']) ||
            empty($remittance_entry['amount'])) { return false; }
        
        /** get type bank transacion for remittance */
        $type_bank_transaction =
            TypeBankTransaction::whereRaw('LCASE(name) = "remesa"')
                ->select('id')
                ->first();

        if(empty($type_bank_transaction)) return false;

        BankTransaction::create([
            'bank_account_id' => $remittance_entry['bank_account_id'],
            'accounting_entrie_id' => $remittance_entry['accounting_entrie_id'],
            'type_bank_transaction_id' => $type_bank_transaction->id,
            'bank_checkbook_id' => null,
            'reference' => $remittance_entry['reference'],
            'date' => $remittance_entry['date'],
            'amount' => $remittance_entry['amount'],
            'description' => $remittance_entry['description'],
            'headline' => null,
            'check_number' => null,
            'status' => 1
        ]);

        return true;
    }
}