<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use App\Http\Requests\VeiculoRequest;
use App\Http\Resources\VeiculoResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource (Listar Veículos).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Veiculo::query();

        // Implementar filtros (ex: por placa, modelo, tipo)
        if ($request->has('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('placa', 'like', $searchTerm)
                  ->orWhere('modelo', 'like', $searchTerm)
                  ->orWhere('cor', 'like', $searchTerm)
                  ->orWhere('tipo', 'like', $searchTerm);
            });
        }
        if ($request->has('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        // Implementar ordenação
        if ($request->has('sort_by') && $request->has('sort_order')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order'));
        } else {
            $query->orderBy('id', 'asc'); // Ordenação padrão
        }

        // Implementar paginação
        $perPage = $request->input('per_page', 10);
        $veiculos = $query->paginate($perPage);

        return VeiculoResource::collection($veiculos);
    }

    /**
     * Store a newly created resource in storage (Criar Veículo).
     *
     * @param  \App\Http\Requests\VeiculoRequest  $request
     * @return \App\Http\Resources\VeiculoResource
     */
    public function store(VeiculoRequest $request): VeiculoResource
    {
        $veiculo = Veiculo::create($request->validated());
        return new VeiculoResource($veiculo);
    }

    /**
     * Display the specified resource (Ver Detalhes do Veículo).
     *
     * @param  \App\Models\Veiculo  $veiculo
     * @return \App\Http\Resources\VeiculoResource
     */
    public function show(Veiculo $veiculo): VeiculoResource
    {
        return new VeiculoResource($veiculo);
    }

    /**
     * Update the specified resource in storage (Atualizar Veículo).
     *
     * @param  \App\Http\Requests\VeiculoRequest  $request
     * @param  \App\Models\Veiculo  $veiculo
     * @return \App\Http\Resources\VeiculoResource
     */
    public function update(VeiculoRequest $request, Veiculo $veiculo): VeiculoResource
    {
        $veiculo->update($request->validated());
        return new VeiculoResource($veiculo);
    }

    /**
     * Remove the specified resource from storage (Excluir Veículo).
     *
     * @param  \App\Models\Veiculo  $veiculo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Veiculo $veiculo): JsonResponse
    {
        // Antes de excluir um veículo, você pode verificar se ele está estacionado
        // ou se há registros de estacionamento associados.
        // Por simplicidade, estamos apenas excluindo aqui.
        $veiculo->delete();
        return response()->json(null, 204); // 204 No Content
    }
}