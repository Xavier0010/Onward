<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_avatar_borders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('avatar_border_id');
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('avatar_border_id')->references('id')->on('avatar_borders')->onDelete('cascade');

            $table->unique(['user_id', 'avatar_border_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_avatar_borders');
    }
};
