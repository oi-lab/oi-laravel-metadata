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
        Schema::create('metadata', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('keywords')->nullable();
            $table->string('author')->nullable();
            $table->string('copyright')->nullable();
            $table->string('language')->nullable();
            $table->string('revisit_after')->nullable();
            $table->string('robots')->nullable();
            $table->string('googlebot')->nullable();
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
        Schema::dropIfExists('metadata');
    }
};
