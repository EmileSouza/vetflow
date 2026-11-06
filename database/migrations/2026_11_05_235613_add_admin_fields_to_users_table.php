<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nenhuma conta recebe permissões por padrão.
            $table->string('papel', 20)->nullable();

            // Contas administrativas precisam ser ativadas.
            $table->boolean('ativo')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['papel', 'ativo']);
        });
    }
};
