<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @link \App\Models\Video
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->text('text')->nullable();

            $table->unsignedBigInteger('duration')->nullable();
            $table->unsignedBigInteger('length')->nullable();

            $table->string('width')->nullable();
            $table->string('height')->nullable();

            $table->timestamps();

            $table->fullText('text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
