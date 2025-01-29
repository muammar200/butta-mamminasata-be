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
            $table->dropColumn('office_hours');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('office_hours')->after('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('office_hours')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('office_hours');
        });
    }
};
