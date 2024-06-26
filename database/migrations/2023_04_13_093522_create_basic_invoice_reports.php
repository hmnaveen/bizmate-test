<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicInvoiceReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_invoice_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->bigInteger('transaction_collection_id')->unsigned()->index();
            $table->foreign('transaction_collection_id')->references('id')->on('basic_transaction_collections')->onDelete('cascade');
            $table->string('invoice_report_file')->nullable();
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
        Schema::dropIfExists('basic_invoice_reports');
    }
}
