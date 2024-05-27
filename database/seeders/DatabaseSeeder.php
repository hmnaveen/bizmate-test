<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(SumbUsersTableSeeder::class);
        $this->call(SumbInvoiceSettingsTableSeeder::class);
        $this->call(SumbTransactionsTableSeeder::class);
        $this->call(SumbClientsTableSeeder::class);
        $this->call(SumbExpensesClientsTableSeeder::class);
        // $this->call(SumbInvoiceDetailsTableSeeder::class);
        // $this->call(SumbInvoiceParticularsTableSeeder::class);
        // $this->call(SumbInvoiceParticularsTempTableSeeder::class);
        $this->call(CreateChartAccountsSeeder::class);
        $this->call(CreateChartAccountsTypeSeeder::class);
        $this->call(CreateInvoiceTaxRatesSeeder::class);
    }
}
