<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbExpenseDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('sumb_expense_details', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->string('expense_number');
            $table->integer('transaction_id')->default(0);
            $table->text('client_name')->nullable();
            $table->text('client_email')->nullable();
            $table->text('client_address')->nullable();
            $table->text('client_phone')->nullable();
          //  $table->text('expense_details')->nullable();
           // $table->double('amount', 15, 2);
            $table->string('status_paid')->default('unpaid');
           // $table->string('invoice_name')->nullable();
            $table->string('expense_email')->nullable();
            $table->string('expense_phone')->nullable();
           // $table->text('expense_terms')->nullable();
            $table->text('logo')->nullable();
          //  $table->string('expense_format')->nullable();
            $table->date('expense_date')->nullable();
            $table->date('expense_due_date')->nullable();
            $table->integer('tax_type');
            $table->double('expense_total_amount', 15, 2);
            $table->double('total_gst')->nullable();
            $table->double('total_amount', 15, 2);
            $table->string('file_upload')->nullable();
            $table->string('file_upload_format')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('sumb_expense_details');
    }
}
