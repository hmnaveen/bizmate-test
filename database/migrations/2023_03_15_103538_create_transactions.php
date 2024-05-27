<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->index();
            $table->bigInteger('transaction_collection_id')->unsigned()->index();
            $table->bigInteger('parts_tax_rate_id')->unsigned()->index();
            $table->bigInteger('parts_chart_accounts_id')->unsigned()->index();
            $table->string('parts_name')->nullable();
            $table->string('parts_code')->nullable();
            $table->string('parts_tax_rate')->nullable();
            $table->integer('parts_quantity')->default(0);
            $table->text('parts_description');
            $table->double('parts_unit_price', 15, 2)->default(0);
            $table->double('parts_gst_amount', 15, 2)->default(0);
            $table->double('parts_amount', 15, 2)->default(0);
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
        Schema::dropIfExists('transactions');
    }
}
?>