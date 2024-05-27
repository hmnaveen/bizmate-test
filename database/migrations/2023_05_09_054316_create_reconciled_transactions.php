<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconciledTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconciled_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->index();
            $table->bigInteger('bank_transaction_id')->unsigned()->index();
            $table->bigInteger('transaction_collection_id')->unsigned()->index();
            $table->boolean('is_reconciled')->default(0);
            $table->date('reconciled_at');
            $table->double('adjustments', 15, 2)->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reconciled_transactions');
    }
}
