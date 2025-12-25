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
            $table->date('ano_letivo_inicio')->nullable()->after('estado');
            $table->date('ano_letivo_fim')->nullable()->after('ano_letivo_inicio');
        });
    }

public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ano_letivo_inicio', 'ano_letivo_fim']);
        });
    }
};
