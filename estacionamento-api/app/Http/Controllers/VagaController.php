<?php

namespace App\Http\Controllers;

use App\Models\Vaga; // Importe o Model Vaga
use App\Http\Requests\StoreVagaRequest; // Importe o StoreVagaRequest
use App\Http\Requests\UpdateVagaRequest; // Importe o UpdateVagaRequest
use App\Http\Resources\VagaResource; // Importe o VagaResource

// Para códigos HTTP use essas importações
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VagaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vaga::query();

        // Filtro por status
        if ($request->has('status') && in_array($request->status, ['livre', 'ocupada', 'interditada'])) {
            $query->where('status', $request->status);
        }

        // Filtro por localização (rua, bairro)
        if ($request->has('localizacao')) {
            $query->where(function ($q) use ($request) {
                $q->where('rua', 'like', '%' . $request->localizacao . '%')
                  ->orWhere('bairro', 'like', '%' . $request->localizacao . '%');
            });
        }

        // Ordenação
        if ($request->has('ordenar_por') && in_array($request->ordenar_por, ['id', 'codigo', 'status'])) {
            $orderDirection = $request->input('ordem', 'asc');
            $query->orderBy($request->ordenar_por, $orderDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('per_page', 15);
        $vagas = $query->paginate($perPage);

        return VagaResource::collection($vagas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVagaRequest $request)
    {
        // Os nomes das colunas aqui virão do $request->validated()
        $vaga = Vaga::create($request->validated());

        return new VagaResource($vaga);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vaga $vaga)
    {
        return new VagaResource($vaga);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVagaRequest $request, Vaga $vaga)
    {
        $vaga->update($request->validated());

        return new VagaResource($vaga);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vaga $vaga)
    {
        $vaga->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
