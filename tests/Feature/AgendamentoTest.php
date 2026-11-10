<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Tutor;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AgendamentoTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | TESTE 1 - Criar um agendamento com sucesso
    |--------------------------------------------------------------------------
    */

    public function test_pode_criar_um_agendamento_com_sucesso()
    {
        // 1. Criar tutor
        $tutor = Tutor::create([
            'nome' => 'Ana',
            'cpf' => '111.111.111-11',
            'telefone' => '1111',
            'email' => 'ana@email.com',
            'endereco' => 'Rua X'
        ]);

        // 2. Criar pet
        $pet = Pet::create([
            'nome' => 'Rex',
            'especie' => 'Cachorro',
            'tutor_id' => $tutor->id
        ]);

        // 3. Criar veterinário
        $vet = Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        // NOVO: Preparar veterinário autorizado.
        // Esta aprovação existe apenas no banco de testes.
        $vet->status_crmv = Veterinario::CRMV_APROVADO;
        $vet->metodo_verificacao = 'manual';
        $vet->ativo = true;
        $vet->save();

        // 4. Autenticar veterinário aprovado
        Sanctum::actingAs($vet);

        // 5. Definir data futura
        $dataHora = now()
            ->addDays(7)
            ->setTime(14, 0)
            ->format('Y-m-d H:i:s');

        // 6. Criar agendamento
        $response = $this->postJson('/api/agendamentos', [
            'data_hora' => $dataHora,
            'veterinario_id' => $vet->id,
            'status' => 'pendente',
            'pet_id' => $pet->id,
        ]);

        // 7. Verificar resultado
        $response->assertStatus(201);

        $this->assertDatabaseHas('agendamentos', [
            'veterinario_id' => $vet->id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 2 - Bloquear agendamento conflitante
    |--------------------------------------------------------------------------
    */

    public function test_nao_deve_permitir_agendamento_conflitante()
    {
        // 1. Criar tutor
        $tutor = Tutor::create([
            'nome' => 'Ana',
            'cpf' => '111.111.111-11',
            'telefone' => '1111',
            'email' => 'ana@email.com',
            'endereco' => 'Rua X'
        ]);

        // 2. Criar pet
        $pet = Pet::create([
            'nome' => 'Rex',
            'especie' => 'Cachorro',
            'tutor_id' => $tutor->id
        ]);

        // 3. Criar veterinário
        $vet = Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        // NOVO: Preparar veterinário autorizado.
        // Esta aprovação existe apenas no banco de testes.
        $vet->status_crmv = Veterinario::CRMV_APROVADO;
        $vet->metodo_verificacao = 'manual';
        $vet->ativo = true;
        $vet->save();

        // 4. Autenticar veterinário aprovado
        Sanctum::actingAs($vet);

        // 5. Definir horário para testar conflito
        $dataHora = now()
            ->addDays(7)
            ->setTime(14, 0)
            ->format('Y-m-d H:i:s');

        // 6. Criar primeiro agendamento
        $primeiroResponse = $this->postJson('/api/agendamentos', [
            'data_hora' => $dataHora,
            'veterinario_id' => $vet->id,
            'status' => 'pendente',
            'pet_id' => $pet->id,
        ]);

        // Confirmar que o primeiro foi criado
        $primeiroResponse->assertStatus(201);

        // 7. Tentar agendar novamente no mesmo horário
        $response = $this->postJson('/api/agendamentos', [
            'data_hora' => $dataHora,
            'veterinario_id' => $vet->id,
            'status' => 'pendente',
            'pet_id' => $pet->id,
        ]);

        // 8. Verificar se o conflito foi bloqueado
        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'veterinario_id'
        ]);
    }
}
