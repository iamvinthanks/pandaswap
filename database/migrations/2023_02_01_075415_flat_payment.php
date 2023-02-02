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
        Schema::create('flat_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('recipient', 100);
            $table->string('amount', 30);
            $table->string('coin', 100);
            $table->enum('type', ['direct','giftcard']);
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
