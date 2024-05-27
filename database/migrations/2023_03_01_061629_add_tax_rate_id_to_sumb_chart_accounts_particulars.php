<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxRateIdToSumbChartAccountsParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_chart_accounts_particulars', function (Blueprint $table) {
            $table->bigInteger('accounts_tax_rate_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sumb_chart_accounts_particulars', function (Blueprint $table) {
            $table->dropColumn('accounts_tax_rate_id');
        });
    }
}
