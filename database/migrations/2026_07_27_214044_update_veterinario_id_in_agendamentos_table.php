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
    Schema::table('agendamentos', function (Blueprint $table) {
        // 1. Remove a coluna antiga de texto
        $table->dropColumn('veterinario');
        
        // 2. Adiciona a nova coluna como chave estrangeira
        $table->foreignId('veterinario_id')->after('pet_id')->constrained('veterinarios')->onDelete('cascade');
        
        // 3. Recria o índice único, mas agora com o ID do veterinário
        $table->unique(['veterinario_id', 'data_hora']);
    });
}

public function down(): void
{
    Schema::table('agendamentos', function (Blueprint $table) {
        $table->dropUnique(['veterinario_id', 'data_hora']);
        $table->dropForeign(['veterinario_id']);
        $table->dropColumn('veterinario_id');
        $table->string('veterinario');
    });
}
};
