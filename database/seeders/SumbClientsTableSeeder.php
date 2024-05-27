<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_clients')->delete();
        
        \DB::table('sumb_clients')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 36,
                'client_name' => 'John Lee',
                'client_email' => 'johnlee@sample.com',
                'client_phone' => '1231231234',
                'client_address' => '61 Bayview Road
Tyringa South Australia
Australia 5671',
                'client_details' => 'Praesent suscipit molestie laoreet. Nam laoreet porta venenatis. Morbi eu urna maximus quam vehicula placerat eu faucibus libero. Praesent ac erat turpis. In vitae euismod magna. Morbi tristique eros et mi ultrices gravida. Nam sagittis imperdiet feugiat. Etiam tincidunt sem eget magna efficitur, sed egestas est eleifend. 

Donec sed euismod enim. Cras euismod, justo at lacinia placerat, leo odio tempor turpis, ac dignissim risus massa eget enim. Praesent laoreet ultrices augue, ut pretium nisl pellentesque id.',
                'default_client' => '0',
                'created_at' => '2022-10-02 17:27:42',
                'updated_at' => '2022-10-02 17:27:42',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 36,
                'client_name' => 'Eric Dickler',
                'client_email' => 'eric.dickler@edraccounting.com.au',
                'client_phone' => '1231231234',
                'client_address' => '83 Albacore Crescent
Kangaloon New South Wales
Australia 2576',
                'client_details' => 'Nullam consequat, ante at vulputate scelerisque, dui nisl congue quam, at congue tortor est a ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse eu sem consectetur, tempus sapien eget, vulputate felis. Donec luctus rhoncus laoreet. Nulla leo leo, congue ut massa nec, pretium euismod eros. 

In sem mauris, porta at ligula id, bibendum ornare magna. Praesent diam tortor, mattis maximus sapien et, pretium finibus ligula. Mauris tempor posuere elit vel hendrerit.

Phasellus blandit orci magna, id venenatis enim venenatis aliquam. Suspendisse maximus mi ac finibus finibus. Praesent volutpat turpis quis sapien luctus egestas. Nunc eget pellentesque lectus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Etiam nec posuere turpis. Quisque nec eros vitae urna ullamcorper porta. 

Maecenas ut vulputate enim. Vestibulum nec augue dictum, mattis erat ut, fringilla purus. Duis sit amet risus congue, sollicitudin sem ut, porttitor metus. Aenean vel diam eget ante imperdiet mollis. Nulla faucibus, lorem vitae vehicula sollicitudin, tellus tellus blandit tellus, quis mollis leo leo a orci. Maecenas vel suscipit odio.',
                'default_client' => '0',
                'created_at' => '2022-10-02 17:44:12',
                'updated_at' => '2022-10-02 17:44:12',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 36,
                'client_name' => 'asdsadas',
                'client_email' => 'asdasd@asd.com',
                'client_phone' => '1231231234',
                'client_address' => 'qwewq eqe qwe qweqw
asd adad ad ad
zxc zxc zxc zxcz xcz',
                'client_details' => 'asdjkh asdaslkdj alsdkja s
asldkajs ldkjas ldkajsd laksjd
alskd alksdjl akjs',
                'default_client' => '0',
                'created_at' => '2022-10-02 19:56:05',
                'updated_at' => '2022-10-02 19:56:05',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}