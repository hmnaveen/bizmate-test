<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index();
            $table->string('bank_user_id');
            $table->string('account_type');
            $table->string('account_id');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('currency');
            $table->double('balance');
            $table->double('avaialable_funds')->nullable();;
            $table->string('instituition');
            $table->double('credit_limit')->nullable();;
            $table->json('class')->nullable();;
            $table->json('transaction_intervals')->nullable();;
            $table->string('account_holder');
            $table->string('connection_id');
            $table->string('status');
            $table->json('links')->nullable();
            $table->dateTime('bank_last_updated');
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
        //
    }
}
