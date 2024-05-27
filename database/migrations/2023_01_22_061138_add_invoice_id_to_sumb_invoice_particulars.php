<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceIdToSumbInvoiceParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_invoice_particulars', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('sumb_invoice_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sumb_invoice_particulars', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
        });
    }
}
