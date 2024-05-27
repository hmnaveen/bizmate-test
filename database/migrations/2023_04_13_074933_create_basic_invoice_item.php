<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicInvoiceItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_invoice_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->bigInteger('invoice_item_tax_rate_id')->unsigned()->index();
            $table->string('invoice_item_name');
            $table->string('invoice_item_code');
            $table->string('invoice_item_description')->nullable();
            $table->double('invoice_item_unit_price', 15, 2)->default(0);
            $table->string('invoice_item_tax_rate')->nullable();
            $table->double('invoice_item_quantity', 15, 2)->default(1);
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
        Schema::dropIfExists('basic_invoice_item');
    }
}
