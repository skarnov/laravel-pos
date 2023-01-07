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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('mobile', 20)->nullable();
            $table->string('image', 100)->nullable();
            $table->decimal('sale_due', 10, 2)->nullable();
            $table->decimal('total_buy', 10, 2)->nullable();
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
        Schema::drop('customers');
    }
};
