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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('product_name', 100);
            $table->string('product_image', 100)->nullable();
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
        Schema::drop('products');
    }
};
