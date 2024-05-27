<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceSentAndStatusToSumbInvoiceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_invoice_details', function (Blueprint $table) {
            $table->boolean('invoice_sent')->default(0);
            $table->enum('invoice_status', ['Unpaid', 'Paid', 'Voided'])->default('Unpaid');
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
            $table->dropColumn('invoice_sent');
            $table->dropColumn('invoice_status');
        });
    }
}
