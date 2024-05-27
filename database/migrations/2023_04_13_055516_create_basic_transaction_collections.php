<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicTransactionCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_transaction_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->integer('transaction_number');
            $table->integer('transaction_ref_number')->nullable();
            $table->enum('transaction_type', ['invoice', 'expense'])->nullable();
            $table->double('sub_total', 15, 2)->default(0);
            $table->double('total_gst', 15, 2)->default(0);
            $table->double('total_amount', 15, 2)->default(0);
            $table->text('logo')->nullable();
            $table->text('invoice_format')->nullable();
            $table->string('default_tax');
            $table->boolean('invoice_sent')->default(0);
            $table->enum('status', ['Unpaid', 'Paid', 'Voided', 'Recalled'])->default('Unpaid');
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('basic_transaction_collections');
    }
}
