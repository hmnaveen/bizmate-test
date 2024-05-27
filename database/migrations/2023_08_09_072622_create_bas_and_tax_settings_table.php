<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasAndTaxSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bas_and_tax_settings', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('sumb_users')->onDelete('cascade');
            $table->string('gst_calculation_period');
            $table->string('gst_accounting_method');
            $table->string('payg_withhold_period');
            $table->string('payg_income_tax_method');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bas_and_tax_settings');
    }
}
