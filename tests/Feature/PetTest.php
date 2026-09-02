<?php

namespace Tests\Feature;

use App\Models\Tutor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PetTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_criar_um_pet_para_um_tutor_existente()
    {
        $tutor = Tutor::create([
            'nome' => 'Carlos Mendes',
            'cpf' => '987.654.321-00',
            'telefone' => '(31) 99999-9999',
            'email' => 'carlos@email.com',
            'endereco' => 'Av. Principal, 500'
        ]);

        $response = $this->postJson('/api/pets', [
            'nome' => 'Rex',
            'especie' => 'Cachorro',
            'raca' => 'Vira-lata',
            'tutor_id' => $tutor->id,
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('pets', [
            'nome' => 'Rex',
            'tutor_id' => $tutor->id
        ]);
    }

    public function test_nao_deve_permitir_criar_pet_sem_tutor_valido()
    {
        $response = $this->postJson('/api/pets', [
            'nome' => 'Miau',
            'especie' => 'Gato',
            'tutor_id' => 999,
        ]);

        $response->assertStatus(422);
    }

    public function test_pode_fazer_upload_da_foto_do_pet_com_sucesso()
    {
        $tutor = Tutor::create([
            'nome' => 'Ana Paula',
            'cpf' => '111.111.111-11',
            'telefone' => '(11) 11111-1111',
            'email' => 'ana@email.com',
            'endereco' => 'Rua X'
        ]);

        // Simula um arquivo de imagem válido (JPG de 500kb)
        $file = UploadedFile::fake()->image('rex.jpg')->size(500);

        $response = $this->postJson('/api/pets', [
            'nome' => 'Rex',
            'especie' => 'Cachorro',
            'tutor_id' => $tutor->id,
            'foto' => $file,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'nome', 'foto_url']);
        
        // Verifica se o arquivo foi "salvo" no disco
        Storage::disk('public')->assertExists('pets/' . $file->hashName());
    }

    public function test_nao_deve_permitir_upload_de_arquivo_invalido_ou_muito_pesado()
    {
        $tutor = Tutor::create([
            'nome' => 'Carlos',
            'cpf' => '222.222.222-22',
            'telefone' => '(22) 22222-2222',
            'email' => 'carlos@email.com',
            'endereco' => 'Rua Y'
        ]);

        // Simula um arquivo PDF (não é imagem!)
        $file = UploadedFile::fake()->create('documento.pdf', 100);

        $response = $this->postJson('/api/pets', [
            'nome' => 'Miau',
            'especie' => 'Gato',
            'tutor_id' => $tutor->id,
            'foto' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['foto']);
    }
}