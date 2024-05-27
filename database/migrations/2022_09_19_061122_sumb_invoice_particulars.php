<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbInvoiceParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sumb_invoice_particulars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->bigInteger('invoice_id')->unsigned()->index();
            $table->integer('invoice_number')->default(0);
            $table->string('invoice_part_name')->nullable();
            $table->string('invoice_part_code')->nullable();
            $table->string('invoice_part_tax_rate')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('part_type')->default('goods');
            $table->text('description');
            $table->double('unit_price', 15, 2)->default(0);
            $table->double('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sumb_invoice_particulars');
    }
}
