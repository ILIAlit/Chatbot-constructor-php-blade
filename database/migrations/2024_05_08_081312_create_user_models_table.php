<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('user_name');
            $table->integer('stage');
            $table->integer('tg_chat_id');
            $table->integer('ttu')->nullable();
            $table->timestamps();
            $table->unsignedBiginteger('telegraph_bot_id');
            $table->foreign('telegraph_bot_id')->references('id')
            ->on('telegraph_bots')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_models');
    }
};