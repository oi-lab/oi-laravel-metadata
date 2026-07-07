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
        Schema::create('json_ld', function (Blueprint $table) {
            $table->id();
            $table->json('graphs')->nullable();
            $table->string('metable_type');
            $table->unsignedBigInteger('metable_id');
            $table->timestamps();

            $table->unique(['metable_type', 'metable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('json_ld');
    }
};
