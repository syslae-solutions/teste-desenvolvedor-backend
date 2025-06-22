<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // Importe o modelo User
use Illuminate\Support\Facades\Hash; // Para checar senhas

class AuthApiTest extends TestCase
{
    use RefreshDatabase; // Garante um banco de dados limpo para cada teste

    protected $user;
    protected $token;

    /**
     * Executado antes de cada método de teste.
     * Cria um usuário e tenta logá-lo para obter um token reutilizável.
     */
    protected function setUp(): void
    {
        parent::setUp(); // Não esqueça de chamar o setUp do pai

        // Cria um usuário de teste no banco de dados para usar no login
        $this->user = User::factory()->create([
            'email' => 'test_user@example.com',
            'password' => Hash::make('password123'), // Hash da senha para o DB
        ]);

        // Tenta logar o usuário recém-criado para obter um token
        $response = $this->postJson('/api/login', [
            'email' => 'test_user@example.com',
            'password' => 'password123', // Senha sem hash para o login
        ]);

        // Aqui o login foi bem-sucedido e pega o token
        $response->assertStatus(200);
        $this->token = $response->json('token');
    }

    /** @test */
    public function it_can_register_a_new_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'New Register User',
            'email' => 'newregister@example.com',
            'password' => 'registerpass123',
            'password_confirmation' => 'registerpass123',
        ]);

        $response->assertStatus(201) // Espera status 201 Created
                 ->assertJsonStructure(['message', 'token', 'user' => ['id', 'name', 'email']]); // Verifica a estrutura JSON da resposta

        // Verifica se o usuário foi realmente salvo no banco de dados
        $this->assertDatabaseHas('users', [
            'email' => 'newregister@example.com',
            'name' => 'New Register User',
        ]);
    }

    /** @test */
    public function it_returns_error_if_registration_email_already_exists()
    {
        // Tenta registrar com o email do usuário criado no setUp()
        $response = $this->postJson('/api/register', [
            'name' => 'Existing Email User',
            'email' => 'test_user@example.com', // Email já existe
            'password' => 'somepassword',
            'password_confirmation' => 'somepassword',
        ]);

        $response->assertStatus(422) // Espera status 422 Unprocessable Entity
                 ->assertJsonValidationErrors(['email']); // Verifica que há um erro de validação para o campo 'email'
    }

    /** @test */
    public function it_can_login_a_user_with_valid_credentials()
    {
        // O login já foi feito no setUp, mas este teste serve para verificar explicitamente o endpoint
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200) // Espera status 200 OK
                 ->assertJsonStructure(['message', 'token', 'user']);

        // Verifica se o token está presente e não é nulo
        $this->assertNotNull($response->json('token'));
    }

    /** @test */
    public function it_returns_error_with_invalid_login_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test_user@example.com',
            'password' => 'wrong-password', // Senha incorreta
        ]);

        $response->assertStatus(422) // Espera status 422 Unprocessable Entity
                 ->assertJsonValidationErrors(['email']); // Laravel geralmente retorna erro no campo que falhou a autenticação
        $this->assertStringContainsString('credenciais fornecidas estão incorretas', $response->json('message')); // Confirma a mensagem de erro
    }

    /** @test */
    public function it_can_logout_an_authenticated_user()
    {
        // Faz logout usando o token obtido no setUp
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/logout');

        $response->assertStatus(200) // Espera status 200 OK
                 ->assertJson(['message' => 'Logout bem-sucedido!']);

        // Tenta acessar uma rota protegida com o token que acabou de ser revogado
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/user'); // /api/user é uma rota protegida padrão

        $response->assertStatus(401) // Espera status 401 Unauthorized
                 ->assertJson(['message' => 'Unauthenticated.']); // Mensagem de não autenticado
    }

    /** @test */
    public function it_can_access_protected_route_with_valid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/user');

        $response->assertStatus(200) // Espera status 200 OK
                 ->assertJson(['id' => $this->user->id, 'email' => $this->user->email]);
    }

    /** @test */
    public function it_cannot_access_protected_route_without_token()
    {
        $response = $this->getJson('/api/user'); // Requisição sem cabeçalho Authorization

        $response->assertStatus(401) // Espera status 401 Unauthorized
                 ->assertJson(['message' => 'Unauthenticated.']);
    }
}