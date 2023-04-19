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
        Schema::create('fundings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('amount');
            $table->integer('unique_amount');
            $table->tinyInteger('status');
            $table->dateTime('status_updated_at');
            $table->text('note')->default('-');
            $table->string('payment_slip')->nullable();
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
        Schema::dropIfExists('fundings');
    }
};
