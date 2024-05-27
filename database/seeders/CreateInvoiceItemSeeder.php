<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateInvoiceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::table('invoice_item')->delete();
        
        \DB::table('invoice_item')->insert(array (
            0 => 
            array (
                'user_id' => 37,
                'invoice_item_name' => 'Item 1',
                'invoice_item_code' => '12345',
                'invoice_item_description' => 'Item one data desc',
                'invoice_item_unit_price' => 10,
                'invoice_item_tax_rate' => 'Tax Exempt(0%)',
                'invoice_item_quantity' => 1,
            ),
            1 => 
            array (
                'user_id' => 37,
                'invoice_item_name' => 'Item 2',
                'invoice_item_description' => 'Item two data desc',
                'invoice_item_unit_price' => 5,
                'invoice_item_tax_rate' => 'Tax Exempt(0%)',
                'invoice_item_quantity' => 1,
                'invoice_item_code' => '1234',
            ),
        ));
    }
}
