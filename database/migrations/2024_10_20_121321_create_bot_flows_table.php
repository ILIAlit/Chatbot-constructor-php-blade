<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bot_flows', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('token')->unique();
            $table->string('name');
            $table->integer('day');
            $table->date('start_date');
            $table->time('time_message_send');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_flows');
    }
};