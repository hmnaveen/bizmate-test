<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_users')->delete();
        
        \DB::table('sumb_users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'accountype' => 'admin',
                'fullname' => 'Dhon Collera Admin',
                'email' => 'dhongens@gmail.com',
                'email_verified_at' => '2022-09-07 06:08:07',
                'password' => 'de143f8a8dcb2be23e3f96a4882f16ca',
                'remember_token' => 'de143f8a8dcb2be23e3f96a4882f16ca',
                'profilepic' => NULL,
                'session_id' => NULL,
                'active' => '1',
                'created_at' => '2022-09-07 06:08:07',
                'updated_at' => '2022-09-07 06:08:07',
            ),
            1 => 
            array (
                'id' => 24,
                'accountype' => 'accountant',
                'fullname' => 'Donniel Collera',
                'email' => 'me@dhonc.com',
                'email_verified_at' => NULL,
                'password' => 'de143f8a8dcb2be23e3f96a4882f16ca',
                'remember_token' => 'da336d5dd90beacdc6bb45eb24407cd0',
                'profilepic' => NULL,
                'session_id' => NULL,
                'active' => '1',
                'created_at' => '2022-09-07 04:50:37',
                'updated_at' => '2022-09-07 04:50:37',
            ),
            2 => 
            array (
                'id' => 36,
                'accountype' => 'user',
                'fullname' => 'Dhon Collera',
                'email' => 'donniel.collera@edraccounting.com.au',
                'email_verified_at' => NULL,
                'password' => 'de143f8a8dcb2be23e3f96a4882f16ca',
                'remember_token' => '49b2b89c7bf907af1645712c247a0027',
                'profilepic' => NULL,
                'session_id' => NULL,
                'active' => '1',
                'created_at' => '2022-09-18 04:09:33',
                'updated_at' => '2022-09-18 04:09:33',
            ),
        ));
        
        
    }
}