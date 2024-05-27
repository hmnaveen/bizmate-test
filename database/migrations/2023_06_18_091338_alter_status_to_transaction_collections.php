<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusToTransactionCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE transaction_collections MODIFY status ENUM('Unpaid', 'Paid', 'Voided', 'Recalled', 'PartlyPaid') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transaction_collections MODIFY status ENUM('Unpaid', 'Paid', 'Voided', 'Recalled') NOT NULL");
    }
}
