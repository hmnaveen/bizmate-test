<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateInvoiceTaxRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sumb_invoice_tax_rates')->delete();
        
        \DB::table('sumb_invoice_tax_rates')->insert(array (
            0 => 
            array (
                'tax_rates' => 0,
                'tax_rates_name' => 'BAS Excluded',
            ),
            1 => 
            array (
                'tax_rates' => 0,
                'tax_rates_name' => 'GST Free Expenses',
            ),
            2 => 
            array (
                'tax_rates' => 0,
                'tax_rates_name' => 'GST Free Income',
            ),
            3 => 
            array (
                'tax_rates' => 10,
                'tax_rates_name' => 'GST on Expenses (10%)',
            ),
            4 => 
            array (
                'tax_rates' => 10,
                'tax_rates_name' => 'GST on Invoice (10%)',
            )
        ));
    }
}
