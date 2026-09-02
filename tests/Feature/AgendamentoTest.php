<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendamentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_um_agendamento_com_sucesso()
    {
        $tutor = Tutor::create(['nome' => 'Ana', 'cpf' => '111.111.111-11', 'telefone' => '1111', 'email' => 'ana@email.com', 'endereco' => 'Rua X']);
        $pet = Pet::create(['nome' => 'Rex', 'especie' => 'Cachorro', 'tutor_id' => $tutor->id]);
        
        // 1. Criamos o veterinário primeiro
        $vet = Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        $response = $this->postJson('/api/agendamentos', [
            'data_hora' => '2026-07-25 14:00',
            'veterinario_id' => $vet->id, // <-- Usando o ID agora!
            'status' => 'pendente',
            'pet_id' => $pet->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('agendamentos', ['veterinario_id' => $vet->id]);
    }

    public function test_nao_deve_permitir_agendamento_conflitante()
    {
        $tutor = Tutor::create(['nome' => 'Ana', 'cpf' => '111.111.111-11', 'telefone' => '1111', 'email' => 'ana@email.com', 'endereco' => 'Rua X']);
        $pet = Pet::create(['nome' => 'Rex', 'especie' => 'Cachorro', 'tutor_id' => $tutor->id]);
        
        $vet = Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        // 1. Primeiro agendamento
        $this->postJson('/api/agendamentos', [
            'data_hora' => '2026-07-25 14:00',
            'veterinario_id' => $vet->id,
            'status' => 'pendente',
            'pet_id' => $pet->id,
        ]);

        // 2. Tentativa de segundo agendamento no mesmo horário com o MESMO veterinário
        $response = $this->postJson('/api/agendamentos', [
            'data_hora' => '2026-07-25 14:00',
            'veterinario_id' => $vet->id, // Mesmo ID!
            'status' => 'pendente',
            'pet_id' => $pet->id,
        ]);

        // 3. Deve falhar com 422
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['veterinario_id']);
    }
}