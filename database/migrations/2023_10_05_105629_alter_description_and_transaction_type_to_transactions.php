<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDescriptionAndTransactionTypeToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('parts_description')->nullable()->change();
        });

        DB::statement("ALTER TABLE transaction_collections MODIFY transaction_type ENUM('invoice','expense','receive_money','spend_money','minor_adjustment') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('parts_description')->nullable(false)->change();
        });

        DB::statement("ALTER TABLE transaction_collections MODIFY transaction_type ENUM('invoice','expense','receive_money','spend_money') NOT NULL");

    }
}
