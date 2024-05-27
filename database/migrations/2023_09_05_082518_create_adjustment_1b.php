<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustment1b extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustment_1b', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('gst_activity_id')->unsigned()->index();
            $table->foreign('gst_activity_id')->references('id')->on('gst_activity_statement')->onDelete('cascade');
            $table->double('adjust_by', 15,2)->default(0);
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjustment_1b');
    }
}
