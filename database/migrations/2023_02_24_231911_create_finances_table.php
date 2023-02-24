<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id');
            $table->foreign('type_id')->references('id')->on('finance_types')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->integer('in');
            $table->integer('out');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('finances');
    }
};
