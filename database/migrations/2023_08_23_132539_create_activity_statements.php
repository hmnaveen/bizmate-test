<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityStatements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_statements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('sumb_users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('activity_statement_status', ['draft', 'finalise']);
            $table->enum('gst_calculation_period', ['monthly', 'quarterly', 'annually'])->nullable();
            $table->enum('payg_withhold_period', ['none', 'monthly', 'quarterly'])->default('none');
            $table->enum('payg_income_tax_method', ['none', 'incometaxamount','incometaxrate'])->default('none');
            $table->double('payment_amount', 15, 2)->default(0);
            $table->enum('payment_type', ['refund', 'payment'])->default('refund');
            $table->string('abn');
            $table->enum('gst_accounting_method', ['cash', 'accrual'])->default('cash');
            $table->double('total_owed_to_ato', 15,2)->default(0);
            $table->double('total_owed_by_ato', 15,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_statements');
    }
}
