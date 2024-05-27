<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbInvoiceSettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_invoice_settings')->delete();
        
        \DB::table('sumb_invoice_settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'invoice_count' => 1,
                'expenses_count' => 1,
                'logo' => NULL,
                'invoice_format' => 0,
                'invoice_name' => NULL,
                'invoice_email' => NULL,
                'invoice_phone' => NULL,
                'invoice_details' => NULL,
                'invoice_footer' => NULL,
                'created_at' => '2022-09-18 11:53:58',
                'updated_at' => '2022-09-18 11:53:58',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 24,
                'invoice_count' => 1,
                'expenses_count' => 1,
                'logo' => NULL,
                'invoice_format' => 0,
                'invoice_name' => NULL,
                'invoice_email' => NULL,
                'invoice_phone' => NULL,
                'invoice_details' => NULL,
                'invoice_footer' => NULL,
                'created_at' => '2022-09-18 11:54:16',
                'updated_at' => '2022-09-18 11:54:16',
            ),
            2 => 
            array (
                'id' => 5,
                'user_id' => 36,
                'invoice_count' => 4,
                'expenses_count' => 4,
                'logo' => NULL,
                'invoice_format' => 0,
                'invoice_name' => NULL,
                'invoice_email' => NULL,
                'invoice_phone' => NULL,
                'invoice_details' => NULL,
                'invoice_footer' => NULL,
                'created_at' => '2022-09-21 15:32:11',
                'updated_at' => '2022-10-02 23:26:46',
            ),
        ));
        
        
    }
}