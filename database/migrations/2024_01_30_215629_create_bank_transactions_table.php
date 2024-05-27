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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('basiq_user_id');
            $table->string('account_id')->index();
            $table->foreign('account_id')->references('account_id')->on('bank_accounts')->onDelete('cascade');
            $table->string('type');
            $table->string('transaction_id')->unique('transaction_id');
            $table->string('status');
            $table->string('description');
            $table->double('amount');
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
            $table->boolean('is_reconciled')->default(0);
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
        Schema::dropIfExists('bank_transactions');
    }
};
