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
        Schema::create('binance_code_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('code', 100);
            $table->string('refrence', 30);
            $table->string('amount', 100);
            $table->string('coin', 100);
            $table->string('coin_redeemed', 100)->nullable();
            $table->string('coin_type',10)->nullable();
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
