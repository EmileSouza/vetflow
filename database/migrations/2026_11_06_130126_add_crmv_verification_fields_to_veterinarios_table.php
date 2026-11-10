<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('veterinarios', function (Blueprint $table) {
            // Estado da verificação profissional
            $table->string('status_crmv', 20)
                ->default('pendente');

            // UF do registro profissional
            $table->string('crmv_uf', 2)
                ->nullable();

            // Método usado para verificar o registro
            $table->string('metodo_verificacao', 20)
                ->nullable();

            // Fonte oficial consultada
            $table->string('fonte_verificacao', 255)
                ->nullable();

            // Data e hora da verificação
            $table->timestamp('crmv_verificado_em')
                ->nullable();

            // Administrador responsável
            $table->foreignId('crmv_verificado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Justificativa para rejeições
            $table->text('motivo_rejeicao_crmv')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('veterinarios', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'crmv_verificado_por'
            );

            $table->dropColumn([
                'status_crmv',
                'crmv_uf',
                'metodo_verificacao',
                'fonte_verificacao',
                'crmv_verificado_em',
                'motivo_rejeicao_crmv',
            ]);
        });
    }
};
