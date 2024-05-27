<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpenseAccountCodeAndNameToSumbExpenseParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_expense_particulars', function (Blueprint $table) {
            $table->string('expense_account_code');
            $table->string('expense_account_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sumb_expense_particulars', function (Blueprint $table) {
            $table->dropColumn('expense_account_code');
            $table->dropColumn('expense_account_name');
        });
    }
}
