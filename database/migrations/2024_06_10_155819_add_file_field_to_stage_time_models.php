<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_time_models', function (Blueprint $table) {
            $table->string('file_src')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('stage_time_models', function (Blueprint $table) {
            $table->dropColumn('file_src');
        });
    }
};