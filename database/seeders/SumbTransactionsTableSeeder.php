<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbTransactionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_transactions')->delete();
        
        \DB::table('sumb_transactions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 36,
                'transaction_type' => 'invoice',
                'transaction_id' => 1,
                'client_name' => 'John Lee',
                'client_email' => 'johnlee@sample.com',
                'client_address' => '61 Bayview Road
Tyringa South Australia
Australia 5671',
                'client_phone' => '1231231234',
                'invoice_details' => 'Praesent suscipit molestie laoreet. Nam laoreet porta venenatis. Morbi eu urna maximus quam vehicula placerat eu faucibus libero. Praesent ac erat turpis. In vitae euismod magna. Morbi tristique eros et mi ultrices gravida. Nam sagittis imperdiet feugiat. Etiam tincidunt sem eget magna efficitur, sed egestas est eleifend. 

Donec sed euismod enim. Cras euismod, justo at lacinia placerat, leo odio tempor turpis, ac dignissim risus massa eget enim. Praesent laoreet ultrices augue, ut pretium nisl pellentesque id.',
                'amount' => 800.0,
                'status_paid' => 'void',
                'invoice_name' => 'Dhon Collera',
                'invoice_email' => 'me@dhonc.com',
                'invoice_phone' => '1231231234',
                'invoice_terms' => 'Terms And Conditions:
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec iaculis, libero nec sollicitudin molestie, purus sapien mattis arcu, et sagittis libero lectus a felis. Mauris sed blandit nibh. Duis bibendum sed leo quis tincidunt. Phasellus nec urna gravida, pulvinar dolor sed, elementum ligula. Nullam fermentum enim quis varius euismod. Nullam ornare libero in mi scelerisque, eget volutpat ante molestie. Phasellus nec purus quam. 

Cras semper, velit quis consequat pellentesque, nibh nunc consectetur eros, id luctus leo purus quis lectus. Nullam malesuada tellus in tortor tempor molestie. Donec ut sagittis est. Nam vitae mollis sapien. 

Vestibulum ac tortor luctus, vehicula dui ut, venenatis elit. Suspendisse dignissim tincidunt ligula vel iaculis.',
                'logo' => '/uploads/a71ed73925a75dae44b71bc161131adb.png',
                'invoice_format' => 'format001',
                'invoice_date' => '2022-10-02',
                'invoice_duedate' => NULL,
                'invoice_pdf' => 'inv20221002172742-1-d09ff1bc57d3e2a42450e9e5fb7d134c.pdf',
                'created_at' => '2022-10-02 17:27:42',
                'updated_at' => '2022-10-02 23:18:56',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 36,
                'transaction_type' => 'invoice',
                'transaction_id' => 2,
                'client_name' => 'Eric Dickler',
                'client_email' => 'eric.dickler@edraccounting.com.au',
                'client_address' => '83 Albacore Crescent
Kangaloon New South Wales
Australia 2576',
                'client_phone' => '1231231234',
                'invoice_details' => 'Nullam consequat, ante at vulputate scelerisque, dui nisl congue quam, at congue tortor est a ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse eu sem consectetur, tempus sapien eget, vulputate felis. Donec luctus rhoncus laoreet. Nulla leo leo, congue ut massa nec, pretium euismod eros. 

In sem mauris, porta at ligula id, bibendum ornare magna. Praesent diam tortor, mattis maximus sapien et, pretium finibus ligula. Mauris tempor posuere elit vel hendrerit.

Phasellus blandit orci magna, id venenatis enim venenatis aliquam. Suspendisse maximus mi ac finibus finibus. Praesent volutpat turpis quis sapien luctus egestas. Nunc eget pellentesque lectus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Etiam nec posuere turpis. Quisque nec eros vitae urna ullamcorper porta. 

Maecenas ut vulputate enim. Vestibulum nec augue dictum, mattis erat ut, fringilla purus. Duis sit amet risus congue, sollicitudin sem ut, porttitor metus. Aenean vel diam eget ante imperdiet mollis. Nulla faucibus, lorem vitae vehicula sollicitudin, tellus tellus blandit tellus, quis mollis leo leo a orci. Maecenas vel suscipit odio.',
                'amount' => 775.0,
                'status_paid' => 'unpaid',
                'invoice_name' => 'Dhon General Services',
                'invoice_email' => 'me@dhonc.com',
                'invoice_phone' => '1231231234',
                'invoice_terms' => 'Terms And Conditions:
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec iaculis, libero nec sollicitudin molestie, purus sapien mattis arcu, et sagittis libero lectus a felis. Mauris sed blandit nibh. Duis bibendum sed leo quis tincidunt. Phasellus nec urna gravida, pulvinar dolor sed, elementum ligula. Nullam fermentum enim quis varius euismod. Nullam ornare libero in mi scelerisque, eget volutpat ante molestie. Phasellus nec purus quam. Cras semper, velit quis consequat pellentesque, nibh nunc consectetur eros, id luctus leo purus quis lectus. Nullam malesuada tellus in tortor tempor molestie. Donec ut sagittis est. Nam vitae mollis sapien. Vestibulum ac tortor luctus, vehicula dui ut, venenatis elit. Suspendisse dignissim tincidunt ligula vel iaculis.',
                'logo' => '/uploads/a71ed73925a75dae44b71bc161131adb.png',
                'invoice_format' => 'format001',
                'invoice_date' => '2022-10-02',
                'invoice_duedate' => NULL,
                'invoice_pdf' => 'inv20221002174412-2-e4cd151c41a9edb04b35ae1608f4f01d.pdf',
                'created_at' => '2022-10-02 17:44:12',
                'updated_at' => '2022-10-02 17:44:12',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 36,
                'transaction_type' => 'invoice',
                'transaction_id' => 3,
                'client_name' => 'asdsadas',
                'client_email' => 'asdasd@asd.com',
                'client_address' => 'qwewq eqe qwe qweqw
asd adad ad ad
zxc zxc zxc zxcz xcz',
                'client_phone' => '1231231234',
                'invoice_details' => 'asdjkh asdaslkdj alsdkja s
asldkajs ldkjas ldkajsd laksjd
alskd alksdjl akjs',
                'amount' => 423.0,
                'status_paid' => 'unpaid',
                'invoice_name' => 'Dhon Collera',
                'invoice_email' => 'donniel.collera@edraccounting.com.au',
                'invoice_phone' => '0400000000',
                'invoice_terms' => 'aslkdja sldkjasl kdjasd',
                'logo' => '/uploads/a71ed73925a75dae44b71bc161131adb.png',
                'invoice_format' => 'format001',
                'invoice_date' => '2022-10-02',
                'invoice_duedate' => NULL,
                'invoice_pdf' => 'inv20221002195605-3-55ce7ce521201374010174b8748a273e.pdf',
                'created_at' => '2022-10-02 19:56:05',
                'updated_at' => '2022-10-02 23:18:51',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 36,
                'transaction_type' => 'expenses',
                'transaction_id' => 1,
                'client_name' => 'Globe Telecoms Inc.',
                'client_email' => NULL,
                'client_address' => NULL,
                'client_phone' => NULL,
                'invoice_details' => 'Internet Services',
                'amount' => 35.0,
                'status_paid' => 'paid',
                'invoice_name' => NULL,
                'invoice_email' => NULL,
                'invoice_phone' => NULL,
                'invoice_terms' => NULL,
                'logo' => NULL,
                'invoice_format' => NULL,
                'invoice_date' => '2022-10-02',
                'invoice_duedate' => NULL,
                'invoice_pdf' => NULL,
                'created_at' => '2022-10-02 22:21:16',
                'updated_at' => '2022-10-02 23:15:57',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 36,
                'transaction_type' => 'expenses',
                'transaction_id' => 2,
                'client_name' => 'Australian Telecoms Inc.',
                'client_email' => NULL,
                'client_address' => NULL,
                'client_phone' => NULL,
                'invoice_details' => 'Phone lines bill',
                'amount' => 57.0,
                'status_paid' => 'paid',
                'invoice_name' => NULL,
                'invoice_email' => NULL,
                'invoice_phone' => NULL,
                'invoice_terms' => NULL,
                'logo' => NULL,
                'invoice_format' => NULL,
                'invoice_date' => '2022-10-02',
                'invoice_duedate' => NULL,
                'invoice_pdf' => NULL,
                'created_at' => '2022-10-02 23:26:20',
                'updated_at' => '2022-10-02 23:26:20',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 36,
                'transaction_type' => 'expenses',
                'transaction_id' => 3,
                'client_name' => 'Australian Telecoms Inc.',
                'client_email' => NULL,
                'client_address' => NULL,
                'client_phone' => NULL,
                'invoice_details' => 'Phone lines bill',
                'amount' => 133.0,
                'status_paid' => 'paid',
                'invoice_name' => NULL,
                'invoice_email' => NULL,
                'invoice_phone' => NULL,
                'invoice_terms' => NULL,
                'logo' => NULL,
                'invoice_format' => NULL,
                'invoice_date' => '2022-10-02',
                'invoice_duedate' => NULL,
                'invoice_pdf' => NULL,
                'created_at' => '2022-10-02 23:26:46',
                'updated_at' => '2022-10-02 23:26:46',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}