<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbExpenseParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('sumb_expense_particulars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->nullable()->index();
            $table->integer('expense_id');
            $table->string('expense_number');
            $table->string('expense_description')->nullable();
            $table->double('item_quantity',15,2)->nullable();
            $table->double('item_unit_price',15,2)->nullable();
            $table->integer('expense_tax')->nullable();
            $table->double('expense_amount',15,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('sumb_expense_particulars');
    }
}
