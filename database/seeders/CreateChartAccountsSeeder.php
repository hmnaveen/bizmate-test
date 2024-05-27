<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateChartAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sumb_chart_accounts')->delete();
        
        \DB::table('sumb_chart_accounts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'chart_accounts_name' => 'Assets'
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 1,
                'chart_accounts_name' => 'Liabilities',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 1,
                'chart_accounts_name' => 'Expenses',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 1,
                'chart_accounts_name' => 'Revenue',
            ),
        ));
    }
}
