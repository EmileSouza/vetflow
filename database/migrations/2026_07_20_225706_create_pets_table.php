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
    Schema::create('pets', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('especie'); // Ex: Cachorro, Gato
        $table->string('raca')->nullable();
        $table->date('data_nascimento')->nullable();
        
        // 👇 AQUI ESTÁ A MÁGICA DO RELACIONAMENTO 👇
        $table->foreignId('tutor_id')->constrained('tutores')->onDelete('cascade');
        
        $table->string('foto_url')->nullable(); // Para guardarmos o caminho da foto depois
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
