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
    Schema::create('agendamentos', function (Blueprint $table) {
        $table->id();
        $table->dateTime('data_hora');
        $table->string('veterinario'); // Pode ser o nome ou CRMV do veterinário
        $table->string('status')->default('pendente'); // pendente, confirmado, concluído, cancelado
        
        // O Pet é obrigatório e, se o Pet for apagado, o agendamento também é (cascade)
        $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
        
        $table->timestamps();

        // 🚨 A REGRA DE OURO DO NEGÓCIO (Nível Banco de Dados) 
        // Isso diz ao MySQL: "NUNCA deixe existir duas linhas com o mesmo veterinário e o mesmo horário!"
        $table->unique(['veterinario', 'data_hora']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
