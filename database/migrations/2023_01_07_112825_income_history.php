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
        Schema::create('income_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->year('year')->nullable();
            $table->decimal('january', 10, 2)->nullable();
            $table->decimal('february', 10, 2)->nullable();
            $table->decimal('march', 10, 2)->nullable();
            $table->decimal('april', 10, 2)->nullable();
            $table->decimal('may', 10, 2)->nullable();
            $table->decimal('june', 10, 2)->nullable();
            $table->decimal('july', 10, 2)->nullable();
            $table->decimal('august', 10, 2)->nullable();
            $table->decimal('september', 10, 2)->nullable();
            $table->decimal('october', 10, 2)->nullable();
            $table->decimal('november', 10, 2)->nullable();
            $table->decimal('december', 10, 2)->nullable();
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
        Schema::drop('income_history');
    }
};
