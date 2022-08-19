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
            $table->smallIncrements('admin_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('user_name', 50);
            $table->enum('admin_sex', array('male', 'female'));
            $table->string('admin_email', 100);
            $table->string('admin_password', 255);
            $table->rememberToken();
            $table->string('admin_image', 100)->nullable();
            $table->string('admin_mobile', 20)->nullable();
            $table->text('admin_address')->nullable();
            $table->enum('admin_status', array('active', 'inactive'));
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
