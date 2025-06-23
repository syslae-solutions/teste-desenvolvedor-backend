<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VagaController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\EstacionamentoController;
use App\Http\Controllers\DashboardController;

// Rotas de autenticação (não protegidas por middleware Sanctum inicialmente)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas por autenticação Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD de Vagas (Rotas RESTful)
    Route::apiResource('vagas', VagaController::class);

    // CRUD de Veículos (Rotas RESTful)
    Route::apiResource('veiculos', VeiculoController::class);

    // Operações de Estacionamento
    Route::post('estacionamento/entrada', [EstacionamentoController::class, 'entrada']);
    Route::post('estacionamento/saida', [EstacionamentoController::class, 'saida']);
    Route::get('estacionamento/historico', [EstacionamentoController::class, 'index']); // Histórico de todas as operações
    Route::get('estacionamento/historico/pdf', [EstacionamentoController::class, 'downloadHistoricoPdf']); // <<< NOVO: Rota para PDF

    // Rotas para o Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/vagas-ocupadas', [DashboardController::class, 'vagasOcupadas']);
        Route::get('/veiculos-por-dia', [DashboardController::class, 'veiculosPorDia']);
        Route::get('/receita-por-periodo', [DashboardController::class, 'receitaPorPeriodo']);
    });
});