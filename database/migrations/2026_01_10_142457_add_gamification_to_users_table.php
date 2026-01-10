<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('current_streak')->default(0); // Dias seguidos atuais
            $table->integer('max_streak')->default(0);     // Recorde do aluno
            $table->date('last_streak_date')->nullable();  // Último dia que contou
            $table->json('badges')->nullable();            // Array de medalhas conquistadas: ['aluno_100', 'on_fire']
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_streak', 'max_streak', 'last_streak_date', 'badges']);
        });
    }
};
