<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbInvoiceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumb_invoice_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->string('invoice_email')->nullable();
            $table->string('invoice_phone')->nullable();
            $table->date('invoice_issue_date');
            $table->date('invoice_due_date');
            $table->integer('invoice_number');
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->double('invoice_sub_total', 15, 2)->default(0);
            $table->double('invoice_total_gst', 15, 2)->default(0);
            $table->double('invoice_total_amount', 15, 2)->default(0);
            $table->text('invoice_desc')->nullable();
            $table->text('invoice_logo')->nullable();
            $table->text('invoice_format')->nullable();
            $table->integer('deault')->default(0);
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
        Schema::dropIfExists('sumb_invoice_details');
    }
}
