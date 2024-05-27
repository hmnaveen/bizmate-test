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
        DB::statement("ALTER TABLE transaction_collections MODIFY transaction_type ENUM('invoice','expense','receive_money','spend_money','minor_adjustment','payment','arprepayment','apprepayment','aroverpayment','apoverpayment') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE transaction_collections MODIFY transaction_type ENUM('invoice','expense','receive_money','spend_money','minor_adjustment','payment') NOT NULL");
    }
};
