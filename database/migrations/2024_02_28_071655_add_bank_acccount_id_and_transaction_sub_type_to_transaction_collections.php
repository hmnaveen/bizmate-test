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
        Schema::table('transaction_collections', function (Blueprint $table) {
            $table->string('bank_account_id')->nullable();
            $table->enum('transaction_sub_type', ['spent', 'received'])->nullable();
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
            $table->dropColumn('bank_account_id');
            $table->dropColumn('transaction_sub_type');
        });

    }
};
