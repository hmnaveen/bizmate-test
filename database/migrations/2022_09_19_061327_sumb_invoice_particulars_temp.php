<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbInvoiceParticularsTemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sumb_invoice_particulars_temp', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->integer('invoice_number')->default(0);
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
        Schema::dropIfExists('sumb_invoice_particulars_temp');
    }
}
