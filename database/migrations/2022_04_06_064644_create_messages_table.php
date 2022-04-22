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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_id')->index();
            $table->foreign('sender_id')->references('id')->on('users');
            $table->unsignedBigInteger('receiver_id')->index();
            $table->foreign('receiver_id')->references('id')->on('users');
            $table->text('message');
            $table->integer('msg_status')->comment("0-unseen,1=seen")->default(0)->index();
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
        Schema::dropIfExists('messages');
    }
};
