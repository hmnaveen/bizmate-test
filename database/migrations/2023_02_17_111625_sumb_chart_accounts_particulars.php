<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbChartAccountsParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumb_chart_accounts_particulars', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignId('chart_accounts_id')->nullable()->index();
            $table->foreignId('chart_accounts_type_id')->nullable()->index();
            $table->string('chart_accounts_particulars_code')->nullable();
            $table->string('chart_accounts_particulars_name')->nullable();
            $table->string('chart_accounts_particulars_description')->nullable();
            $table->string('chart_accounts_particulars_tax')->nullable();
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
        Schema::dropIfExists('sumb_chart_accounts_particulars');
    }
}
