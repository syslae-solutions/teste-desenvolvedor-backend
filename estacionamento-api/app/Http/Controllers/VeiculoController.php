<?php

namespace App\Http\Controllers;

// Importa o Model Veiculo para interagir com a tabela veiculos no banco de dados.
use App\Models\Veiculo;
// Importa o StoreVeiculoRequest, que contém as regras de validação para criar um novo veículo.
use App\Http\Requests\StoreVeiculoRequest;
// Importa o UpdateVeiculoRequest, que contém as regras de validação para atualizar um veículo existente.
use App\Http\Requests\UpdateVeiculoRequest;
// Importa o VeiculoResource, que é usado para formatar a saída JSON dos veículos na API.
use App\Http\Resources\VeiculoResource;

// Importações padrão do Laravel para lidar com requisições HTTP e respostas.
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Exibe uma listagem de recursos (Veículos).
     * Este método é responsável por retornar todos os veículos ou uma lista filtrada/ordenada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Inicia uma nova query (consulta) no Model Veiculo.
        $query = Veiculo::query();

        // Verifica se a requisição contém um parâmetro placa.
        // Se sim, filtra os veículos por essa placa.
        if ($request->has('placa')) {
            $query->where('placa', $request->placa);
        }

        // Verifica se a requisição contém um parâmetro cor.
        // Se sim, filtra os veículos por essa cor, usando like para busca parcial (ex: "ver" encontraria "vermelho").
        if ($request->has('cor')) {
            $query->where('cor', 'like', '%' . $request->cor . '%');
        }

        // Verifica se a requisição contém um parâmetro modelo.
        // Filtra os veículos por modelo, usando 'like' para busca parcial.
        if ($request->has('modelo')) {
            $query->where('modelo', 'like', '%' . $request->modelo . '%');
        }

        // Verifica se a requisição contém um parâmetro tipo.
        // Filtra os veículos por tipo (exato, 'carro' ou 'moto').
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Verifica se a requisição contém um parâmetro 'ordenar_por'.
        // E se o valor desse parâmetro é um dos campos permitidos para ordenação.
        if ($request->has('ordenar_por') && in_array($request->ordenar_por, ['id', 'placa', 'cor', 'modelo', 'tipo'])) {
            // Pega a direção da ordenação ('asc' para ascendente, 'desc' para descendente).
            // O padrão é asc se nenhuma ordenação for especificada.
            $orderDirection = $request->input('ordem', 'asc');
            // Aplica a ordenação à query.
            $query->orderBy($request->ordenar_por, $orderDirection);
        } else {
            // Se nenhum parâmetro de ordenação válido for fornecido, ordena por 'id' ascendente por padrão.
            $query->orderBy('id', 'asc');
        }

        // Obtém o número de itens por página a partir do parâmetro 'per_page' na requisição.
        // O padrão é 15 itens por página se 'per_page' não for fornecido.
        $perPage = $request->input('per_page', 15);
        // Executa a query e pagina os resultados.
        $veiculos = $query->paginate($perPage);

        // Retorna a coleção de veículos paginados.
        // VeiculoResource::collection() é usado para transformar cada item da coleção (cada Veiculo)
        // usando as regras definidas no VeiculoResource, garantindo um formato de resposta JSON consistente.
        return VeiculoResource::collection($veiculos);
    }

    /**
     * Store a newly created resource in storage.
     * Armazena um novo recurso (Veículo) no banco de dados.
     * Este método é chamado quando uma requisição POST é feita para criar um veículo.
     *
     * @param  \App\Http\Requests\StoreVeiculoRequest  $request
     * @return \App\Http\Resources\VeiculoResource
     */
    public function store(StoreVeiculoRequest $request)
    {
        // O Laravel executa automaticamente as regras de validação definidas em StoreVeiculoRequest.
        // Se a validação falhar, uma exceção é lançada e uma resposta de erro 422 é retornada.
        // Se a validação for bem-sucedida, $request->validated() retorna um array
        // com apenas os dados validados e prontos para serem salvos.
        $veiculo = Veiculo::create($request->validated());

        // Retorna o veículo recém-criado usando o VeiculoResource.
        // Por padrão, o retorno de um Resource após a criação resultará em um status HTTP 201 Created.
        return new VeiculoResource($veiculo);
    }

    /**
     * Display the specified resource.
     * Exibe o recurso (Veículo) especificado.
     * Este método é chamado quando uma requisição GET é feita para buscar um único veículo.
     *
     * @param  \App\Models\Veiculo  $veiculo
     * @return \App\Http\Resources\VeiculoResource
     */
    public function show(Veiculo $veiculo)
    {
        // O Laravel usa o "Route Model Binding" aqui: ele automaticamente encontra
        // um veículo no banco de dados com base no ID fornecido na URL da rota
        // (ex: /api/veiculos/1) e injeta a instância do Model 'Veiculo' diretamente.
        // Se o veículo não for encontrado, o Laravel retornará automaticamente um 404 Not Found.
        return new VeiculoResource($veiculo);
    }

    /**
     * Update the specified resource in storage.
     * Atualiza o recurso (Veículo) especificado no banco de dados.
     * Este método é chamado quando uma requisição PUT ou PATCH é feita para atualizar um veículo.
     *
     * @param  \App\Http\Requests\UpdateVeiculoRequest  $request
     * @param  \App\Models\Veiculo  $veiculo
     * @return \App\Http\Resources\VeiculoResource
     */
    public function update(UpdateVeiculoRequest $request, Veiculo $veiculo)
    {
        // Assim como no método store, o UpdateVeiculoRequest valida os dados.
        // $request->validated() retorna apenas os dados válidos para a atualização.
        $veiculo->update($request->validated());

        // Retorna o veículo atualizado usando o VeiculoResource.
        return new VeiculoResource($veiculo);
    }

    /**
     * Remove the specified resource from storage.
     * Remove o recurso (Veículo) especificado do banco de dados.
     * Este método é chamado quando uma requisição DELETE é feita para excluir um veículo.
     *
     * @param  \App\Models\Veiculo  $veiculo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Veiculo $veiculo)
    {
        $veiculo->delete(); // Exclui o registro do veículo do banco de dados.

        // Retorna uma resposta JSON vazia com o status HTTP 204 No Content.
        // Este status é ideal para operações de exclusão bem-sucedidas onde não há conteúdo
        // para retornar ao cliente.
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}