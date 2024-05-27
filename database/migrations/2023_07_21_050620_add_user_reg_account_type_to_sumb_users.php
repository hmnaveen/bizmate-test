<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRegAccountTypeToSumbUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE sumb_users MODIFY accountype ENUM('admin','manager','accountant','user','user_reg','user_pro') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE sumb_users MODIFY accountype ENUM('admin','manager','accountant','user','user_reg','user_pro') NOT NULL");
    }
}
