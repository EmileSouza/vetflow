<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAgendamentosPeriodoTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | TESTE 1 - Contar agendamentos por semana
    |--------------------------------------------------------------------------
    */

    public function test_admin_consulta_agendamentos_das_ultimas_quatro_semanas(): void
    {
        $admin = $this->criarUsuario(User::PAPEL_ADMIN);

        // Criar os registros necessários para o teste.
        [$pet, $vet] = $this->criarPetEVeterinario();

        // Uma consulta em cada semana.
        $this->criarAgendamento($pet, $vet, 27);
        $this->criarAgendamento($pet, $vet, 15);
        $this->criarAgendamento($pet, $vet, 8);
        $this->criarAgendamento($pet, $vet, 0);

        // Esta consulta é anterior aos 28 dias.
        $this->criarAgendamento($pet, $vet, 28);

        // Esta consulta é futura e não entra no gráfico.
        $this->criarAgendamento($pet, $vet, -1);

        $response = $this
            ->actingAs($admin, 'admin')
            ->getJson(
                '/admin/dashboard/agendamentos-por-periodo'
            );

        $response->assertOk();

        // Somente quatro consultas estão no período.
        $response->assertJsonPath(
            'data.total_periodo',
            4
        );

        $response->assertJsonCount(
            4,
            'data.semanas'
        );

        $response->assertJsonPath(
            'data.semanas.0.quantidade',
            1
        );

        $response->assertJsonPath(
            'data.semanas.1.quantidade',
            1
        );

        $response->assertJsonPath(
            'data.semanas.2.quantidade',
            1
        );

        $response->assertJsonPath(
            'data.semanas.3.quantidade',
            1
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 2 - Investidor consulta indicadores agregados
    |--------------------------------------------------------------------------
    */

    public function test_investidor_pode_consultar_grafico(): void
    {
        $investidor = $this->criarUsuario(
            User::PAPEL_INVESTIDOR
        );

        $response = $this
            ->actingAs($investidor, 'admin')
            ->getJson(
                '/admin/dashboard/agendamentos-por-periodo'
            );

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'periodo',
                    'total_periodo',
                    'semanas',
                ],
            ]);

        // Não devem aparecer dados pessoais.
        $response->assertJsonMissingPath(
            'data.veterinarios'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 3 - Visitante sem sessão não pode consultar
    |--------------------------------------------------------------------------
    */

    public function test_visitante_nao_pode_consultar_grafico(): void
    {
        $this->getJson(
            '/admin/dashboard/agendamentos-por-periodo'
        )->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 4 - Administrador inativo não pode consultar
    |--------------------------------------------------------------------------
    */

    public function test_admin_inativo_nao_pode_consultar_grafico(): void
    {
        $admin = $this->criarUsuario(
            User::PAPEL_ADMIN,
            false
        );

        $this
            ->actingAs($admin, 'admin')
            ->getJson(
                '/admin/dashboard/agendamentos-por-periodo'
            )
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 5 - Veterinário não acessa indicadores admin
    |--------------------------------------------------------------------------
    */

    public function test_veterinario_nao_pode_consultar_grafico(): void
    {
        $vet = Veterinario::create([
            'nome' => 'Veterinario Teste',
            'email' => 'vet.grafico@example.test',
            'password' => 'SenhaForte123!',
            'crmv' => 'TESTE-GRAFICO-001',
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);

        $this
            ->actingAs($vet, 'web')
            ->getJson(
                '/admin/dashboard/agendamentos-por-periodo'
            )
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS AUXILIARES
    |--------------------------------------------------------------------------
    */

    private function criarUsuario(
        string $papel,
        bool $ativo = true
    ): User {
        $usuario = User::factory()->create();

        $usuario->papel = $papel;
        $usuario->ativo = $ativo;
        $usuario->save();

        return $usuario;
    }

    private function criarPetEVeterinario(): array
    {
        $tutor = Tutor::create([
            'nome' => 'Tutor Teste',
            'cpf' => '123.456.789-00',
            'telefone' => '31999999999',
            'email' => 'tutor.grafico@example.test',
            'endereco' => 'Rua Teste, 100',
        ]);

        $pet = Pet::create([
            'nome' => 'Rex',
            'especie' => 'Cachorro',
            'tutor_id' => $tutor->id,
        ]);

        $vet = Veterinario::create([
            'nome' => 'Veterinario Grafico',
            'email' => 'vet.grafico@example.test',
            'password' => 'SenhaForte123!',
            'crmv' => 'TESTE-GRAFICO-001',
            'cidade' => 'Ibirite',
            'uf' => 'MG',
        ]);

        return [$pet, $vet];
    }

    private function criarAgendamento(
        Pet $pet,
        Veterinario $vet,
        int $diasAtras
    ): void {
        $dataHora = now()
            ->subDays($diasAtras)
            ->setTime(14, 0);

        Agendamento::create([
            'data_hora' => $dataHora,
            'veterinario_id' => $vet->id,
            'pet_id' => $pet->id,
            'status' => 'pendente',
        ]);
    }
}
