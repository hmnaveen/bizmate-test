<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reconciled_transactions', function (Blueprint $table) {
            $table->bigInteger('bank_transaction_id')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reconciled_transactions', function (Blueprint $table) {
            $table->bigInteger('bank_transaction_id')->nullable(false)->change();
        });
    }
};
