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
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('fk_admin_id')->nullable();
            $table->enum('type', array('success', 'warning', 'error'));
            $table->string('name', 150);
            $table->string('ip_address', 45)->nullable();
            $table->string('visitor_country', 50)->nullable();
            $table->string('visitor_state', 100)->nullable();
            $table->string('visitor_city', 100)->nullable();
            $table->string('visitor_address', 150)->nullable();
            $table->time('created_time');
            $table->date('created_date');
            $table->smallInteger('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('activities');
    }
};
