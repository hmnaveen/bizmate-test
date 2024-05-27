<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSumbUserDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumb_user_details', function (Blueprint $table) {

            $table->unsignedBigInteger('sumb_user')->index();
            $table->string( 'address' ,254)->nullable();
            $table->string( 'state' ,254)->nullable();
            $table->string( 'city' ,254)->nullable();
            $table->string( 'suburb' ,254)->nullable();
            $table->string( 'zip' , 10 )->nullable();
            $table->string( 'photo' )->nullable();
            $table->string( 'mobile_number' ,20)->nullable();
            $table->string( 'country_code' ,4)->nullable();

        });

        Schema::table('sumb_user_details', function (Blueprint $table) {
            $table->foreign('sumb_user')
            ->references('id')->on('sumb_users')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sumb_user_details');
    }
}
