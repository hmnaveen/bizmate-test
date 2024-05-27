<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentOptionToTransactionCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_collections', function (Blueprint $table) {
            $table->enum('payment_option', ['direct_payment', 'pre_payment', 'over_payment'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_collections', function (Blueprint $table) {
            $table->dropColumn('payment_date');
        });
    }
}
