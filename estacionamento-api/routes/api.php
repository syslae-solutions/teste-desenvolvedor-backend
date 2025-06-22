<?php

use App\Http\Controllers\VagaController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\EstacionamentoController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rota de teste para verificar o usuário autenticado (já existente)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ADICIONAR ESTAS ROTAS PARA AUTENTICAÇÃO (LOGIN/REGISTRO)
// Estas rotas NÃO devem ser protegidas pelo middleware 'auth:sanctum',
// pois o usuário precisa acessá-las para obter um token.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ADICIONAR ESTA ROTA PARA LOGOUT (DEVE SER PROTEGIDA)
// O usuário precisa estar autenticado para poder fazer logout e revogar seu token.
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


// ENVOLVA SUAS ROTAS DE RECURSO NO MIDDLEWARE 'auth:sanctum'
// Isso fará com que todas as rotas de 'vagas', 'veiculos' e 'estacionamentos'
// exijam um token de autenticação válido do Sanctum.
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('vagas', VagaController::class);
    Route::apiResource('veiculos', VeiculoController::class);
    Route::apiResource('estacionamentos', EstacionamentoController::class);
});