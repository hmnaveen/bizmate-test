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
            $table->dropForeign('reconciled_transactions_transaction_collection_id_foreign');
            $table->dropIndex('reconciled_transactions_transaction_collection_id_index');
            $table->dropColumn('transaction_collection_id');

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

        });
    }
};
