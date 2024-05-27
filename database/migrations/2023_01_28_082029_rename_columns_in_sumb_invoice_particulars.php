<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsInSumbInvoiceParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_invoice_particulars', function (Blueprint $table) {
            // $table->renameColumn('invoice_part_name', 'invoice_parts_name');
            // $table->renameColumn('invoice_part_code', 'invoice_parts_code');
            // $table->renameColumn('invoice_part_tax_rate', 'invoice_parts_tax_rate');
            // $table->renameColumn('quantity', 'invoice_parts_quantity');
            // $table->renameColumn('part_type', 'invoice_parts_type');
            // $table->renameColumn('description', 'invoice_parts_description');
            // $table->renameColumn('unit_price', 'invoice_parts_unit_price');
            // $table->renameColumn('amount', 'invoice_parts_amount');
        });
        DB::statement('ALTER TABLE `sumb_invoice_particulars` CHANGE `quantity` `invoice_parts_quantity` INTEGER DEFAULT 0 ;');
        DB::statement('ALTER TABLE `sumb_invoice_particulars` CHANGE `description` `invoice_parts_description` TEXT(255);');
        DB::statement('ALTER TABLE `sumb_invoice_particulars` CHANGE `unit_price` `invoice_parts_unit_price` DOUBLE(15, 2) DEFAULT 0;');
        DB::statement('ALTER TABLE `sumb_invoice_particulars` CHANGE `amount` `invoice_parts_amount` DOUBLE(255, 2) DEFAULT 0 ;');
        DB::statement('ALTER TABLE `sumb_invoice_particulars` CHANGE `invoice_part_name` `invoice_parts_name` VARCHAR(255);');
        DB::statement('ALTER TABLE `sumb_invoice_particulars` CHANGE `invoice_part_code` `invoice_parts_code` VARCHAR(255);');
        DB::statement('ALTER TABLE `sumb_invoice_particulars` CHANGE `invoice_part_tax_rate` `invoice_parts_tax_rate` VARCHAR(255);');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sumb_invoice_particulars', function (Blueprint $table) {
            // $table->renameColumn('invoice_parts_name', 'invoice_part_name');
            // $table->renameColumn('invoice_parts_code', 'invoice_part_code');
            // $table->renameColumn('invoice_parts_tax_rate', 'invoice_part_tax_rate');
            // $table->renameColumn('invoice_parts_quantity', 'quantity');
            // $table->renameColumn('invoice_parts_type', 'part_type');
            // $table->renameColumn('invoice_parts_description', 'description');
            // $table->renameColumn('invoice_parts_unit_price', 'unit_price');
            // $table->renameColumn('invoice_parts_amount', 'unit_price');
        });
    }
}
