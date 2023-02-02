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
        Schema::create('history_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('unique_code',20);
            $table->enum('type',['sell_crypto','buy_crypto','sell_code','buy_code']);
            $table->string('amount', 100)->nullable();
            $table->integer('recipient_id')->nullable();
            $table->integer('crypto_payment_id')->nullable();
            $table->integer('flat_payment_id')->nullable();
            $table->integer('binance_code_payment_id')->nullable();
            $table->enum('status', ['waiting_payment','on_process', 'completed', 'expired']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
