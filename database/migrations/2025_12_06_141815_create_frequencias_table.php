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
    Schema::create('frequencias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('disciplina_id')->constrained('disciplinas')->onDelete('cascade');
        $table->date('data'); // Data da aula
        $table->boolean('presente')->default(false); // false = Faltou (vamos contar as faltas ou presenças?)
        // DICA: No TCC você diz "O sistema divide Presenças / Total"[cite: 109], então vamos salvar:
        // true = Presente, false = Faltou.
        $table->string('observacao')->nullable(); // Ex: "Atestado médico"
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequencias');
    }
};
