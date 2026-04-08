<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->index('text');
        });

        Schema::table('voices', function (Blueprint $table) {
            $table->index('text');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->index('text');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['text']);
        });

        Schema::table('voices', function (Blueprint $table) {
            $table->dropIndex(['text']);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex(['text']);
        });
    }
};
