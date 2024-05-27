<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateChartAccountsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sumb_chart_accounts_type')->delete();
        
        \DB::table('sumb_chart_accounts_type')->insert(array (
            0 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 1,
                'chart_accounts_type' => 'Current Asset'
            ),
            1 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 1,
                'chart_accounts_type' => 'Fixed Asset'
            ),
            2 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 1,
                'chart_accounts_type' => 'Inventory'
            ),
            3 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 1,
                'chart_accounts_type' => 'Non-current Asset'
            ),
            4 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 2,
                'chart_accounts_type' => 'Current Liability'
            ),
            5 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 2,
                'chart_accounts_type' => 'Liability'
            ),
            6 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 2,
                'chart_accounts_type' => 'Non-current Liability'
            ),
            7 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 3,
                'chart_accounts_type' => 'Depreciation'
            ),
            8 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 3,
                'chart_accounts_type' => 'Direct Costs'
            ),
            9 => 
            array (
                'user_id' => 1,
                'chart_accounts_id' => 3,
                'chart_accounts_type' => 'Expense'
            ),
        ));
    }
}
