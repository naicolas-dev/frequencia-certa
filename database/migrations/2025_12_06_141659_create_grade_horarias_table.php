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
    Schema::create('grade_horarias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('disciplina_id')->constrained('disciplinas')->onDelete('cascade');
        $table->tinyInteger('dia_semana'); // 1 = Segunda, 2 = Terça ... 5 = Sexta
        $table->time('horario_inicio'); // Ex: 07:00
        $table->time('horario_fim');    // Ex: 07:50
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_horarias');
    }
};
