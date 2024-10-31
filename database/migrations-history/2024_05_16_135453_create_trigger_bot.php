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
        Schema::create('telegraph_bot_trigger_model', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('telegraph_bot_id');
            $table->unsignedBiginteger('trigger_model_id');


            $table->foreign('telegraph_bot_id')->references('id')
                 ->on('telegraph_bots')->onDelete('cascade');
            $table->foreign('trigger_model_id')->references('id')
                ->on('trigger_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegraph_bot_trigger_model');
    }
};