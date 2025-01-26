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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->decimal('price', 15, 2);
            $table->enum('type', ['perumahan', 'kavling']);
            $table->string('phone');
            $table->string('office_hours');
            $table->string('address');
            $table->string('latitude');
            $table->string('longitude');
            $table->text('description');
            $table->timestamps();

            //relationship category
            $table->foreign('category_id')->references('id')->on('categories');

            //relationship user
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
