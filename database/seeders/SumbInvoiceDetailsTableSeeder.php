<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbInvoiceDetailsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_invoice_details')->delete();
        
        \DB::table('sumb_invoice_details')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 36,
                // 'invoice_name' => 'Dhon Collera',
                'invoice_email' => 'me@dhonc.com',
                'invoice_phone' => '1231231234',
                'invoice_desc' => 'Terms And Conditions:
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec iaculis, libero nec sollicitudin molestie, purus sapien mattis arcu, et sagittis libero lectus a felis. Mauris sed blandit nibh. Duis bibendum sed leo quis tincidunt. Phasellus nec urna gravida, pulvinar dolor sed, elementum ligula. Nullam fermentum enim quis varius euismod. Nullam ornare libero in mi scelerisque, eget volutpat ante molestie. Phasellus nec purus quam. 

Cras semper, velit quis consequat pellentesque, nibh nunc consectetur eros, id luctus leo purus quis lectus. Nullam malesuada tellus in tortor tempor molestie. Donec ut sagittis est. Nam vitae mollis sapien. 

Vestibulum ac tortor luctus, vehicula dui ut, venenatis elit. Suspendisse dignissim tincidunt ligula vel iaculis.',
                'invoice_logo' => '/uploads/a71ed73925a75dae44b71bc161131adb.png',
                'invoice_format' => 'format001',
               'invoice_issue_date'=> '2023-03-07',
                'invoice_due_date'=> '2023-03-07',
                'invoice_number'=> '0001',
                'client_name'=>'Naveen',
                'invoice_default_tax' => 'tax_inclusive',
                'deault' => 0,
                'created_at' => '2022-10-02 17:27:42',
                'updated_at' => '2022-10-02 17:27:42',
            ),
        ));
        
        
    }
}