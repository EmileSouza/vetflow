<?php

namespace Tests\Feature;

use App\Models\Tutor;
use App\Models\Veterinario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PetTest extends TestCase
{
    use RefreshDatabase;

    // Executado automaticamente antes de cada teste
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Criar veterinário fictício
        $vet = Veterinario::create([
            'nome' => 'Dr. Carlos',
            'email' => 'carlos@vet.com',
            'password' => 'senha123',
            'crmv' => 'CRMV-123'
        ]);

        // NOVO: Preparar o veterinário para os testes.
        // A aprovação simulada funciona apenas no ambiente
        // local ou de testes, conforme o middleware.
        $vet->status_crmv = Veterinario::CRMV_APROVADO;
        $vet->metodo_verificacao = 'simulado';
        $vet->ativo = true;
        $vet->save();

        // 2. Autenticar veterinário autorizado
        Sanctum::actingAs($vet);
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 1 - Cadastrar pet para tutor existente
    |--------------------------------------------------------------------------
    */

    public function test_pode_criar_um_pet_para_um_tutor_existente()
    {
        // 1. Criar tutor
        $tutor = Tutor::create([
            'nome' => 'Carlos Mendes',
            'cpf' => '987.654.321-00',
            'telefone' => '(31) 99999-9999',
            'email' => 'carlos@email.com',
            'endereco' => 'Av. Principal, 500'
        ]);

        // 2. Cadastrar pet
        $response = $this->postJson('/api/pets', [
            'nome' => 'Rex',
            'especie' => 'Cachorro',
            'raca' => 'Vira-lata',
            'tutor_id' => $tutor->id,
        ]);

        // 3. Verificar se foi criado
        $response->assertStatus(201);

        $this->assertDatabaseHas('pets', [
            'nome' => 'Rex',
            'tutor_id' => $tutor->id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 2 - Não permitir pet sem tutor válido
    |--------------------------------------------------------------------------
    */

    public function test_nao_deve_permitir_criar_pet_sem_tutor_valido()
    {
        // 1. Tentar cadastrar com tutor inexistente
        $response = $this->postJson('/api/pets', [
            'nome' => 'Miau',
            'especie' => 'Gato',
            'tutor_id' => 999,
        ]);

        // 2. Verificar se foi recusado
        $response->assertStatus(422);
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 3 - Upload válido da foto do pet
    |--------------------------------------------------------------------------
    */

    public function test_pode_fazer_upload_da_foto_do_pet_com_sucesso()
    {
        // 1. Simular armazenamento de arquivos
        Storage::fake('public');

        // 2. Criar tutor
        $tutor = Tutor::create([
            'nome' => 'Ana Paula',
            'cpf' => '111.111.111-11',
            'telefone' => '(11) 11111-1111',
            'email' => 'ana@email.com',
            'endereco' => 'Rua X'
        ]);

        // 3. Criar imagem fictícia de 500 KB
        $file = UploadedFile::fake()
            ->image('rex.jpg')
            ->size(500);

        // 4. Enviar cadastro com foto
        $response = $this->postJson('/api/pets', [
            'nome' => 'Rex',
            'especie' => 'Cachorro',
            'tutor_id' => $tutor->id,
            'foto' => $file,
        ]);

        // 5. Verificar resposta
        $response->assertStatus(201);

        $response->assertJsonStructure([
            'id',
            'nome',
            'foto_url'
        ]);

        // 6. Verificar se a foto foi armazenada
        Storage::disk('public')->assertExists(
            'pets/' . $file->hashName()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TESTE 4 - Bloquear upload inválido
    |--------------------------------------------------------------------------
    */

    public function test_nao_deve_permitir_upload_de_arquivo_invalido_ou_muito_pesado()
    {
        // 1. Simular armazenamento
        Storage::fake('public');

        // 2. Criar tutor
        $tutor = Tutor::create([
            'nome' => 'Carlos',
            'cpf' => '222.222.222-22',
            'telefone' => '(22) 22222-2222',
            'email' => 'carlos@email.com',
            'endereco' => 'Rua Y'
        ]);

        // 3. Criar arquivo PDF inválido
        $file = UploadedFile::fake()
            ->create('documento.pdf', 100);

        // 4. Tentar cadastrar pet com PDF
        $response = $this->postJson('/api/pets', [
            'nome' => 'Miau',
            'especie' => 'Gato',
            'tutor_id' => $tutor->id,
            'foto' => $file,
        ]);

        // 5. Verificar se o upload foi recusado
        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['foto']);
    }
}
