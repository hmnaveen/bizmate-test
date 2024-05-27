<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaygIncomeTaxInstalment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payg_income_tax_instalment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('activity_id')->unsigned()->index();
            $table->foreign('activity_id')->references('id')->on('activity_statements')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason_code_t4')->nullable();
            $table->double('payg_income_tax_instalment_5a', 15,2)->default(0);
            $table->double('payg_income_tax_instalment_credit', 15,2)->default(0);
            $table->json('option_1')->default(null);
            $table->json('option_2')->default(null);
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payg_income_tax_instalment');
    }
}
