<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaygWithheldActivityStatement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payg_withheld_activity_statement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('activity_id')->unsigned()->index();
            $table->foreign('activity_id')->references('id')->on('activity_statements')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->double('payg_tax_withheld', 15,2)->default(0);
            $table->double('payg_withheld_w1', 15,2)->default(0);
            $table->double('payg_withheld_w2', 15,2)->default(0);
            $table->double('payg_withheld_w3', 15,2)->default(0);
            $table->double('payg_withheld_w4', 15,2)->default(0);
            $table->double('payg_withheld_w5', 15,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payg_withheld_activity_statement');
    }
}
