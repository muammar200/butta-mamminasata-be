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
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['address', 'type', 'longitude', 'latitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->enum('type', ['rumah', 'kavling'])->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });
    }
};
