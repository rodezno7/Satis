<?php

use App\BankAccount;
use App\BankTransaction;
use App\TransactionPayment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyBankToBankAccountOnTransactionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** Get actual banks */
        $banks =
            TransactionPayment::where('method', 'bank_transfer')
                ->where('transfer_receiving_bank', '>', '0')
                ->select('transfer_receiving_bank')
                ->groupBy('transfer_receiving_bank')
                ->get();
        
        $bank_ids = [];
        foreach($banks as $b) {
            $bank_account = BankAccount::where('bank_id', $b->transfer_receiving_bank)->first();

            $ids = TransactionPayment::where('method', 'bank_transfer')
                ->where('transfer_receiving_bank', $b->transfer_receiving_bank)
                ->select('id')->get()->toArray();

            if(!empty($bank_account)) {
                $bank_ids[] = [
                    'bank_id' => $b->transfer_receiving_bank,
                    'bank_account_id' => $bank_account->id,
                    'ids' => $ids
                ];
            }
        }

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->dropForeign('transaction_payments_transfer_receiving_bank_foreign');
            $table->dropIndex('transaction_payments_transfer_receiving_bank_foreign');

            /** set bank_transfer_bank column as NULL */
            DB::statement('UPDATE transaction_payments SET transfer_receiving_bank = NULL WHERE transfer_receiving_bank IS NOT NULL');

            $table->foreign('transfer_receiving_bank')
                ->references('id')
                ->on('bank_accounts');
        });

        /** set bank accounts */
        foreach($bank_ids as $b){
            TransactionPayment::whereIn('id', $b['ids'])
                ->update(['transfer_receiving_bank' => $b['bank_account_id']]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            //
        });
    }
}
