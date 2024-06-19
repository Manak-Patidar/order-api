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
        Schema::create('dealer', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique;
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('longitude');
            $table->string('latitude');
            $table->string('concered_name');
            $table->string('dealer_code');
            $table->string('number');
            $table->string('designation');
            $table->string('brands');
            $table->string('dealear_active');
            $table->string('emp_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealer');
    }
};