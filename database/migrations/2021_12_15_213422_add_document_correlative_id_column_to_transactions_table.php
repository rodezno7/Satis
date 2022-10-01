<?php

use App\DocumentCorrelative;
use App\Transaction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocumentCorrelativeIdColumnToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('document_correlative_id')->nullable()->after('document_types_id');
            $table->foreign("document_correlative_id")->references("id")->on("document_correlatives");
        });

        $sales = Transaction::where('type', 'sell')
            ->whereNotNull('document_types_id')
            ->get();

        if (! empty($sales)) {
            foreach ($sales as $sale) {
                $document_correlative = DocumentCorrelative::where('business_id', $sale->business_id)
                    ->where('location_id', $sale->location_id)
                    ->where('document_type_id', $sale->document_types_id)
                    ->whereRaw('CONVERT(initial, UNSIGNED INTEGER) <= ?', [$sale->correlative])
                    ->whereRaw('CONVERT(final, UNSIGNED INTEGER) >= ?', [$sale->correlative])
                    ->first();
    
                $sale->document_correlative_id = ! empty($document_correlative) ? $document_correlative->id : null;
                $sale->save();
            }
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
            $table->dropForeign(['document_correlative_id']);
            $table->dropColumn('document_correlative_id');
        });
    }
}
