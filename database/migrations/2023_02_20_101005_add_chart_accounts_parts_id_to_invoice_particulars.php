<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChartAccountsPartsIdToInvoiceParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_invoice_particulars', function (Blueprint $table) {
            $table->bigInteger('invoice_chart_accounts_parts_id')->unsigned()->index();
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
            $table->dropColumn('invoice_chart_accounts_parts_id');
        });
    }
}
