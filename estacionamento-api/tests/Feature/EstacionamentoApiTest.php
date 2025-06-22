<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vaga;
use App\Models\Veiculo;
use App\Models\Estacionamento; // Importe o modelo Estacionamento
use Carbon\Carbon; // Para manipular datas e horas

class EstacionamentoApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);
        $this->token = $response->json('token');
    }

    /** @test */
    public function it_can_register_a_vehicle_entry_into_a_free_spot()
    {
        $vaga = Vaga::factory()->create(['status' => 'livre']);
        $veiculo = Veiculo::factory()->create();

        $entryTime = Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/estacionamentos', [
            'vaga_id' => $vaga->id,
            'veiculo_id' => $veiculo->id,
            'entrada' => $entryTime,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'vaga_id' => $vaga->id,
                         'veiculo_id' => $veiculo->id,
                         'saida' => null, // Deve ser nulo na entrada
                         'vaga' => ['status' => 'ocupada'] // Status da vaga deve ser ocupada
                     ]
                 ]);

        $this->assertDatabaseHas('estacionamentos', [
            'vaga_id' => $vaga->id,
            'veiculo_id' => $veiculo->id,
            'saida' => null,
        ]);
        $this->assertDatabaseHas('vagas', [
            'id' => $vaga->id,
            'status' => 'ocupada', // Confirma que o status da vaga mudou
        ]);
    }

    /** @test */
    public function it_prevents_entry_if_spot_is_occupied()
    {
        $vaga = Vaga::factory()->create(['status' => 'ocupada']);
        $veiculo = Veiculo::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/estacionamentos', [
            'vaga_id' => $vaga->id,
            'veiculo_id' => $veiculo->id,
            'entrada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(409) 
                 ->assertJson(['message' => 'Não é possível estacionar: A vaga selecionada está ocupada.']);

        $this->assertDatabaseMissing('estacionamentos', [ // Garante que não foi criado um registro
            'vaga_id' => $vaga->id,
            'veiculo_id' => $veiculo->id,
            'saida' => null,
        ]);
    }

    /** @test */
    public function it_prevents_entry_if_spot_is_interditada()
    {
        $vaga = Vaga::factory()->create(['status' => 'interditada']);
        $veiculo = Veiculo::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/estacionamentos', [
            'vaga_id' => $vaga->id,
            'veiculo_id' => $veiculo->id,
            'entrada' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(409)
                 ->assertJson(['message' => 'Não é possível estacionar: A vaga selecionada está interditada.']);
    }

    /** @test */
    public function it_can_register_vehicle_exit_and_calculate_time_and_value()
    {
        $vaga = Vaga::factory()->create(['status' => 'ocupada']);
        $veiculo = Veiculo::factory()->create();

        // Crie um registro de estacionamento 'ocupado'
        $estacionamento = Estacionamento::factory()->create([
            'vaga_id' => $vaga->id,
            'veiculo_id' => $veiculo->id,
            'entrada' => Carbon::now()->subHours(2)->subMinutes(30), // 2h30m atrás
            'saida' => null,
        ]);

        $exitTime = Carbon::now()->format('Y-m-d H:i:s');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/estacionamentos/' . $estacionamento->id, [ // Use PUT/PATCH com o ID
            'saida' => $exitTime,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $estacionamento->id,
                         'saida' => Carbon::parse($exitTime)->toIso8601String(), // Compare com o formato ISO
                         'vaga' => ['status' => 'livre'] // Vaga deve voltar a ser 'livre'
                     ]
                 ]);

        // Recupere o registro para verificar o tempo e valor calculados
        $updatedEstacionamento = $estacionamento->fresh();
        $this->assertNotNull($updatedEstacionamento->saida);
        $this->assertNotNull($updatedEstacionamento->tempo_total);
        $this->assertNotNull($updatedEstacionamento->valor);

        // Verifique o valor do tempo total e o valor calculado (2h30m = 2.5h * 2 = 5.00)
        $this->assertEquals(5.00, $updatedEstacionamento->valor);
        $this->assertDatabaseHas('vagas', [
            'id' => $vaga->id,
            'status' => 'livre', // Confirma que a vaga foi liberada
        ]);
    }

    /** @test */
    public function it_prevents_exit_if_vehicle_is_not_currently_parked()
    {
        // Crie um registro de estacionamento que já tenha saída
        $estacionamento = Estacionamento::factory()->create([
            'saida' => Carbon::now()->subHour(), // Já saiu
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/estacionamentos/' . $estacionamento->id, [
            'saida' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(409)
                 ->assertJson(['message' => 'Não é possível registrar a saída: O veículo não está estacionado nesta vaga.']);
    }
}
