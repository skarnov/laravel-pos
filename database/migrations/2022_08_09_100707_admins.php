<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('user_name', 10);
            $table->enum('sex', array('male', 'female'));
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->rememberToken();
            $table->string('image', 100)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->enum('status', array('active', 'inactive'));
            $table->time('created_time')->nullable();
            $table->date('created_date')->nullable();
            $table->smallInteger('created_by')->nullable();
            $table->time('modified_time')->nullable();
            $table->date('modified_date')->nullable();
            $table->smallInteger('modified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('admins');
    }
};
