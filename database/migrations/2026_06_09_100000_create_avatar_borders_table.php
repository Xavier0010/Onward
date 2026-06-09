<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('avatar_borders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rarity'); // common, rare, epic, legendary
            $table->integer('price');
            $table->string('color'); // hex or css color
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('avatar_borders');
    }
};
