<?php

namespace App\Http\Controllers;

use App\Models\Vaga;
use App\Http\Requests\VagaRequest;
use App\Http\Resources\VagaResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VagaController extends Controller
{
    /**
     * Display a listing of the resource (Listar Vagas).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    { 
        // if(!Vaga::exits()){
        //     return VagaResource::collection(collect());
        // }
        
        $query = Vaga::query();

        // Implementar filtros por status e localização (rua, número, bairro)
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('localizacao')) {
            $searchTerm = '%' . $request->input('localizacao') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('rua', 'like', $searchTerm)
                  ->orWhere('numero', 'like', $searchTerm)
                  ->orWhere('bairro', 'like', $searchTerm);
            });
        }

        // Implementar ordenação
        if ($request->has('sort_by') && $request->has('sort_order')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order'));
        } else {
            $query->orderBy('id', 'asc'); // Ordenação padrão
        }

        // Implementar paginação
        $perPage = $request->input('per_page', 10);
        $vagas = $query->paginate($perPage);

        return VagaResource::collection($vagas);
    }

    /**
     * Store a newly created resource in storage (Criar Vaga).
     *
     * @param  \App\Http\Requests\VagaRequest  $request
     * @return \App\Http\Resources\VagaResource|\Illuminate\Http\JsonResponse
     */
    public function store(VagaRequest $request): VagaResource
    {
        // Separar a localização em rua, número, bairro antes de criar
        $localizacaoArray = explode(',', $request->input('localizacao'));
        // Garante que as variáveis existam mesmo se a string estiver incompleta
        $rua = trim($localizacaoArray[0] ?? '');
        $numero = trim($localizacaoArray[1] ?? '');
        $bairro = trim($localizacaoArray[2] ?? '');

        $vaga = Vaga::create([
            'codigo' => $request->input('codigo'),
            'rua' => $rua,
            'numero' => $numero,
            'bairro' => $bairro,
            'status' => $request->input('status'),
        ]);

        return new VagaResource($vaga);
    }

    /**
     * Display the specified resource (Ver Detalhes da Vaga).
     *
     * @param  \App\Models\Vaga  $vaga
     * @return \App\Http\Resources\VagaResource
     */
    public function show(Vaga $vaga): VagaResource
    {
        return new VagaResource($vaga);
    }

    /**
     * Update the specified resource in storage (Atualizar Vaga).
     *
     * @param  \App\Http\Requests\VagaRequest  $request
     * @param  \App\Models\Vaga  $vaga
     * @return \App\Http\Resources\VagaResource
     */
    public function update(VagaRequest $request, Vaga $vaga): VagaResource
    {
        // Separar a localização em rua, número, bairro antes de atualizar
        $localizacaoArray = explode(',', $request->input('localizacao'));
        // Garante que as variáveis existam mesmo se a string estiver incompleta
        $rua = trim($localizacaoArray[0] ?? '');
        $numero = trim($localizacaoArray[1] ?? '');
        $bairro = trim($localizacaoArray[2] ?? '');

        $vaga->update([
            'codigo' => $request->input('codigo'),
            'rua' => $rua,
            'numero' => $numero,
            'bairro' => $bairro,
            'status' => $request->input('status'),
        ]);

        return new VagaResource($vaga);
    }

    /**
     * Remove the specified resource from storage (Excluir Vaga).
     *
     * @param  \App\Models\Vaga  $vaga
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Vaga $vaga): JsonResponse
    {
        $vaga->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
