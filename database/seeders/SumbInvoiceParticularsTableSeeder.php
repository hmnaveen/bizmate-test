<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbInvoiceParticularsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_invoice_particulars')->delete();
        
        \DB::table('sumb_invoice_particulars')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 36,
                'invoice_number' => 1,
                'quantity' => 10,
                'part_type' => 'goods',
                'description' => 'Sample Goods',
                'unit_price' => 25.0,
                'amount' => 250.0,
                'created_at' => '2022-10-02 17:26:00',
                'updated_at' => '2022-10-02 17:26:00',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 36,
                'invoice_number' => 1,
                'quantity' => 0,
                'part_type' => 'services',
                'description' => 'Sample Seivices',
                'unit_price' => 0.0,
                'amount' => 550.0,
                'created_at' => '2022-10-02 17:26:15',
                'updated_at' => '2022-10-02 17:26:15',
            ),
            2 => 
            array (
                'id' => 4,
                'user_id' => 36,
                'invoice_number' => 2,
                'quantity' => 0,
                'part_type' => 'services',
                'description' => 'Sample Services:
Praesent suscipit molestie laoreet. Nam laoreet porta venenatis. Morbi eu urna maximus quam vehicula placerat eu faucibus libero. Praesent ac erat turpis. In vitae euismod magna.',
                'unit_price' => 0.0,
                'amount' => 275.0,
                'created_at' => '2022-10-02 17:44:06',
                'updated_at' => '2022-10-02 17:44:06',
            ),
            3 => 
            array (
                'id' => 3,
                'user_id' => 36,
                'invoice_number' => 2,
                'quantity' => 5,
                'part_type' => 'goods',
                'description' => 'Sample Goods',
                'unit_price' => 100.0,
                'amount' => 500.0,
                'created_at' => '2022-10-02 17:43:04',
                'updated_at' => '2022-10-02 17:43:04',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 36,
                'invoice_number' => 3,
                'quantity' => 0,
                'part_type' => 'services',
                'description' => 'asldkj alsdkja lsdj
asdlkas dlkasjd laskj
aslkdj laskjd
asldkj alskdj alksj',
                'unit_price' => 0.0,
                'amount' => 300.0,
                'created_at' => '2022-10-02 19:55:29',
                'updated_at' => '2022-10-02 19:55:29',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 36,
                'invoice_number' => 3,
                'quantity' => 0,
                'part_type' => 'services',
                'description' => 'aslkda sdjhkj wiuq eoiqwueq owieuo 
qwoie qwoeiuq oweiu qwe\'
qowieuoqwieuoq wieuoq iwue',
                'unit_price' => 0.0,
                'amount' => 123.0,
                'created_at' => '2022-10-02 19:55:46',
                'updated_at' => '2022-10-02 19:55:46',
            ),
        ));
        
        
    }
}