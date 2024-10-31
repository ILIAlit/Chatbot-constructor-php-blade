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
        Schema::create('stage_models', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->integer('pause');
            $table->integer('order');
            $table->unsignedBigInteger('chain_model_id');
            $table->foreign('chain_model_id')->references('id')->on('chain_models')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_models');
    }
};