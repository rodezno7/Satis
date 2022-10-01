<?php

use App\Transaction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocumentDateColumnToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('document_date')->nullable()->after('transaction_date');
        });

        $transactions = Transaction::whereIn('type', ['purchase', 'expense'])->get();

        foreach ($transactions as $transaction) {
            $transaction->document_date = \Carbon::parse($transaction->transaction_date)->format('Y-m-d');
            $transaction->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('document_date');
        });
    }
}
