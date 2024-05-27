<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGstActivityStatement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gst_activity_statement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('activity_id')->unsigned()->index();
            $table->foreign('activity_id')->references('id')->on('activity_statements')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->double('total_sales_g1', 15,2)->default(0);
            $table->double('gst_sales_1a', 15,2)->default(0);
            $table->double('gst_purchases_1b', 15,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gst_activity_statement');
    }
}
