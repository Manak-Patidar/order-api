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
        Schema::create('pjp_report', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('concerned_person');
            $table->date('date');
            $table->date('remarks');
            $table->date('payment_stauts');
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
        Schema::dropIfExists('pjp_report');
    }
};
