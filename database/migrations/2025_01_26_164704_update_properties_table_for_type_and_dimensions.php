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
            $table->dropColumn('type');
            $table->enum('type', ['rumah', 'kavling'])->default('rumah')->after('price');

            
            $table->string('size')->nullable()->after('type'); // Kolom untuk ukuran properti
            $table->decimal('area', 10, 2)->nullable()->after('size'); // Kolom untuk luas properti
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['type']);
            $table->enum('type', ['perumahan', 'kavling'])->after('price');

            // Hapus kolom 'size' dan 'area'
            $table->dropColumn(['size', 'area']);
        });
    }
};
