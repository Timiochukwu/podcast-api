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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('audio_url');
            $table->integer('duration_in_seconds');
            $table->text('transcript')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('slug');
            $table->index('is_featured');
            $table->index(['podcast_id', 'published_at']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};