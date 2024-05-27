<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbExpensesClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_expenses_clients')->delete();
        
        \DB::table('sumb_expenses_clients')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 36,
                'client_name' => 'GLOBE TELECOMS INC.',
                'client_description' => 'Internet Services',
                'client_comments' => NULL,
                'created_at' => '2022-10-02 22:21:16',
                'updated_at' => '2022-10-02 22:21:16',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 36,
                'client_name' => 'Australian Telecoms Inc.',
                'client_description' => 'Phone lines bill',
                'client_comments' => NULL,
                'created_at' => '2022-10-02 23:26:20',
                'updated_at' => '2022-10-02 23:26:20',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}