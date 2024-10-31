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
        Schema::create('mail_flow_students_models', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('offset')->default(0);
            $table->integer('flow_id');
            $table->string('text');
            $table->string('file_path')->nullable();
            $table->boolean('succeeded')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_flow_students_models');
    }
};