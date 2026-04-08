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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('name');
            $table->dropColumn('password');

            $table->bigInteger('telegram_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->boolean('is_bot');

            $table->string('language_code')->default('en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('telegram_id');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('username');
            $table->dropColumn('is_bot');
            $table->string('name')->change();
            $table->string('email')->change();
            $table->string('password')->change();
            $table->dropColumn('language_code');
        });
    }
};
