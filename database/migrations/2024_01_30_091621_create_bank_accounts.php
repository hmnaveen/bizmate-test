<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('basiq_user_id');
            $table->foreign('basiq_user_id')->references('basiq_user_id')->on('sumb_users')->onDelete('cascade');
            $table->string('account_id')->unique();
            $table->string('account_type');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('instituition');
            $table->string('currency');
            $table->double('balance');
            $table->double('avaialable_funds')->nullable();
            $table->double('credit_limit')->nullable();
            $table->json('class')->nullable();
            $table->json('transaction_intervals')->nullable();
            $table->string('account_holder');
            $table->string('connection_id');
            $table->string('status');
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('bank_accounts');
    }
};
