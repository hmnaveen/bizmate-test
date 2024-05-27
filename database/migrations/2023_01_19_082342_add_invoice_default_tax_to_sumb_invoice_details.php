<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceDefaultTaxToSumbInvoiceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_invoice_details', function (Blueprint $table) {
            $table->string('invoice_default_tax');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sumb_invoice_details', function (Blueprint $table) {
            $table->dropColumn('invoice_default_tax');
        });
    }
}
