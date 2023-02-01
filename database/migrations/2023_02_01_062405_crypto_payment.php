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
        Schema::create('CryptoPayment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('payment_wallet', 42);
            $table->string('private_key', 66);
            $table->string('amount', 100);
            $table->enum('coin', ['TRX','USDT']);
            $table->string('paid_amount', 100)->nullable();
            $table->string('payout_tx', 66)->nullable();
            $table->enum('status', ['waiting_payment', 'completed', 'expired']);
            $table->timestamp('expired_at')->nullable();
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
