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
        Schema::create('message_days_flows', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('text');
            $table->time('time_send');
            $table->string('file_path')->nullable();
            $table->unsignedBiginteger('flow_day_id');
            $table->foreign('flow_day_id')
          ->references('id')->on('flow_days')
          ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_days_flows');
    }
};