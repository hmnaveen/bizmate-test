<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index();
            $table->string('bank_user_id');
            $table->string('type');
            $table->string('transaction_id');
            $table->string('status');
            $table->string('description');
            $table->double('amount');
            $table->string('account_id');
            $table->double('balance')->nullable();
            $table->string('direction');
            $table->string('class');
            $table->string('instituition_id');
            $table->string('connection_id');
            $table->string('enrich')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->dateTime('post_date');
            $table->json('sub_class')->nullable();
            $table->json('links')->nullable();
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
        Schema::dropIfExists('user_bank_transactions');
    }
}
