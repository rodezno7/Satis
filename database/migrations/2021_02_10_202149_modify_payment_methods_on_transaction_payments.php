<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPaymentMethodsOnTransactionPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `transaction_payments` CHANGE `method` `method`
            ENUM('cash','card','check','bank_transfer','credit')
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `transaction_payments` CHANGE
            `bank_account_number` `check_account` VARCHAR(191)
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `transaction_payments` CHANGE `card_holder_name`
            `card_holder_name` VARCHAR(191) CHARACTER SET utf8mb4
            COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `transaction_no`");

        Schema::table('transaction_payments', function (Blueprint $table) {
            /** Drop useless columns */
            $table->dropColumn('card_transaction_number');
            $table->dropColumn('card_number');
            $table->dropColumn('card_month');
            $table->dropColumn('card_year');
            $table->dropColumn('card_security');

            /** CHECK */
            $table->unsignedInteger('check_bank')
                ->nullable()
                ->default(null)
                ->after('check_account');
            $table->string('check_account_owner')
                ->nullable()
                ->default(null)
                ->after('check_bank');

            $table->foreign('check_bank')
                ->references('id')
                ->on('banks');
            
            /** TRANSFER */
            $table->string('transfer_ref_no')
                ->nullable()
                ->default(null)
                ->after('check_account_owner');
            $table->unsignedInteger('transfer_issuing_bank')
                ->nullable()
                ->default(null)
                ->after('transfer_ref_no');
            $table->string('transfer_destination_account')
                ->nullable()
                ->default(null)
                ->after('transfer_issuing_bank');
            $table->unsignedInteger('transfer_receiving_bank')
                ->nullable()
                ->default(null)
                ->after('transfer_destination_account');

            $table->foreign('transfer_issuing_bank')
                ->references('id')
                ->on('banks');
            $table->foreign('transfer_receiving_bank')
                ->references('id')
                ->on('banks');
            
            /** CARD */
            $table->string('card_authotization_number')
                ->nullable()
                ->default(null)
                ->after('card_holder_name');
            $table->unsignedInteger('card_pos')
                ->nullable()
                ->default(null)
                ->after('card_type');

            $table->foreign('card_pos')
                ->references('id')
                ->on('pos');

            /** CREDIT */
            $table->unsignedInteger('credit_payment_term')
                ->nullable()
                ->default(null)
                ->after('transfer_receiving_bank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            //
        });
    }
}
