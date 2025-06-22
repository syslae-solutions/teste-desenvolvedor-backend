<?php

namespace App\Http\Controllers;

// Importa os Models necessários para interagir com o banco de dados
use App\Models\Estacionamento;
use App\Models\Vaga; // Para poder manipular o status da vaga
use App\Models\Veiculo; // Para poder manipular o status do veículo

// Importa os FormRequests para validação de entrada
use App\Http\Requests\StoreEstacionamentoRequest;
use App\Http\Requests\UpdateEstacionamentoRequest;

// Importa o Resource para formatar as respostas da API
use App\Http\Resources\EstacionamentoResource;
use App\Http\Resources\VagaResource;

// Importações padrão do Laravel
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon; // Para manipular datas e horas e calcular tempo_total

class EstacionamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Exibe uma listagem de recursos (registros de Estacionamento).
     * Este método é responsável por retornar todos os registros de estacionamento,
     * ou uma lista filtrada/ordenada/paginada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Inicia uma nova query no Model Estacionamento.
        // Adicionamos 'vaga' e 'veiculo' com 'with' para carregar os relacionamentos.
        $query = Estacionamento::with(['vaga', 'veiculo'])->query();

        // Filtro por vaga_id
        if ($request->has('vaga_id')) {
            $query->where('vaga_id', $request->vaga_id);
        }

        // Filtro por veiculo_id
        if ($request->has('veiculo_id')) {
            $query->where('veiculo_id', $request->veiculo_id);
        }

        // Filtro para registros ativos (sem saída registrada)
        if ($request->has('ativos') && $request->ativos == 'true') {
            $query->whereNull('saida');
        }

        // Filtro por data de entrada (exemplo: '2025-06-21')
        if ($request->has('data_entrada')) {
            $query->whereDate('entrada', $request->data_entrada);
        }

        // --- Lógica de Ordenação ---
        if ($request->has('ordenar_por') && in_array($request->ordenar_por, ['id', 'entrada', 'saida', 'valor', 'tempo_total'])) {
            $orderDirection = $request->input('ordem', 'desc'); // Padrão desc para ver os mais recentes primeiro
            $query->orderBy($request->ordenar_por, $orderDirection);
        } else {
            $query->orderBy('entrada', 'desc'); // Ordem padrão: as entradas mais recentes primeiro
        }

        // --- Lógica de Paginação ---
        $perPage = $request->input('per_page', 15); // Número de itens por página, padrão 15
        $estacionamentos = $query->paginate($perPage); // Pagina os resultados

        // Retorna a coleção de registros de estacionamento formatados pelo EstacionamentoResource.
        return EstacionamentoResource::collection($estacionamentos);
    }

    /**
     * Store a newly created resource in storage.
     * Armazena um novo registro de Estacionamento (entrada de veículo) no banco de dados.
     *
     * @param  \App\Http\Requests\StoreEstacionamentoRequest  $request
     * @return \App\Http\Resources\EstacionamentoResource|\Illuminate\Http\JsonResponse // Adicionado JsonResponse ao tipo de retorno
     */
    public function store(StoreEstacionamentoRequest $request)
    {
        // Valida os dados da requisição. Se a validação falhar, uma resposta de erro 422 é retornada.
        $validatedData = $request->validated();

        // RECUPERA A VAGA PARA VERIFICAR SEU STATUS
        $vaga = Vaga::find($validatedData['vaga_id']);

        // VERIFICA SE A VAGA ESTÁ OCUPADA OU INTERDITADA
        // Se a vaga for encontrada e seu status não for 'livre', impede a entrada.
        if ($vaga && $vaga->status !== 'livre') {
            // Retorna uma resposta JSON com status 409 Conflict (ou 422 Unprocessable Entity)
            // indicando que a requisição não pode ser processada devido a um conflito de estado.
            return response()->json([
                'message' => 'Não é possível estacionar: A vaga selecionada está ' . $vaga->status . '.',
                'errors' => [
                    'vaga_id' => ['A vaga selecionada não está disponível. Status atual: ' . $vaga->status]
                ]
            ], Response::HTTP_CONFLICT); // 409 Conflict ou Response::HTTP_UNPROCESSABLE_ENTITY (422)
        }

        // Define os campos 'saida', 'tempo_total' e 'valor' como nulos na criação,
        // pois eles serão preenchidos apenas quando o veículo sair.
        $validatedData['saida'] = null;
        $validatedData['tempo_total'] = null;
        $validatedData['valor'] = null;

        // Cria o registro de estacionamento no banco de dados.
        $estacionamento = Estacionamento::create($validatedData);

        // --- Lógica de Negócio: Atualizar Status da Vaga para 'ocupada' ---
        if ($vaga) {
            $vaga->update(['status' => 'ocupada']);
        }

        // Retorna o registro de estacionamento recém-criado, formatado pelo Resource.
        return new EstacionamentoResource($estacionamento);
    }

    /**
     * Display the specified resource.
     * Exibe o recurso (registro de Estacionamento) especificado.
     *
     * @param  \App\Models\Estacionamento  $estacionamento
     * @return \App\Http\Resources\EstacionamentoResource
     */
    public function show(Estacionamento $estacionamento)
    {
        // O Laravel usa Route Model Binding: encontra o registro de estacionamento pelo ID da rota.
        // Se não encontrar, retorna 404.
        // Adiciona load para carregar os relacionamentos'vaga e veiculo antes de retornar,
        // garantindo que o Resource tenha esses dados para inclusão.
        $estacionamento->load(['vaga', 'veiculo']);

        // Retorna o registro de estacionamento formatado pelo EstacionamentoResource.
        return new EstacionamentoResource($estacionamento);
    }

    /**
     * Update the specified resource in storage.
     * Atualiza o recurso (registro de Estacionamento) especificado.
     * Este método é primariamente usado para marcar a saída de um veículo e calcular o custo.
     *
     * @param  \App\Http\Requests\UpdateEstacionamentoRequest  $request
     * @param  \App\Models\Estacionamento  $estacionamento
     * @return \App\Http\Resources\EstacionamentoResource|\Illuminate\Http\JsonResponse // Adicionado JsonResponse ao tipo de retorno
     */
    public function update(UpdateEstacionamentoRequest $request, Estacionamento $estacionamento)
    {
        // REGRAS DE NEGÓCIO: NÃO PERMITIR SAÍDA SE O VEÍCULO JÁ NÃO ESTIVER ESTACIONADO.
        // Se a requisição contiver 'saida' e o registro de estacionamento JÁ possuir uma 'saida'
        // (ou seja, o veículo já saiu), impede a operação e retorna um erro.
        if ($request->has('saida') && !is_null($estacionamento->saida)) {
            return response()->json([
                'message' => 'Este registro de estacionamento já possui uma data de saída. O veículo não está mais estacionado aqui.',
                'errors' => [
                    'saida' => ['O veículo já não está estacionado.']
                ]
            ], Response::HTTP_CONFLICT); // 409 Conflict ou 422 Unprocessable Entity
        }

        // Valida os dados da requisição.
        $validatedData = $request->validated();

        // --- Lógica de Negócio: Marcar Saída e Calcular Tempo/Valor ---
        // Verifica se a data de saída foi fornecida na requisição E se o registro
        // ainda não tem uma data de saída (ou seja, o veículo ainda está estacionado).
        if ($request->has('saida') && is_null($estacionamento->saida)) {
            // Converte a string de entrada para um objeto Carbon para facilitar a manipulação de datas.
            $entrada = Carbon::parse($estacionamento->entrada);
            $saida = Carbon::parse($validatedData['saida']);

            // Calcula o tempo total em minutos.
            $tempoTotal = $saida->diffInMinutes($entrada);

            // Calcula o valor total. Exemplo: R$ 2,00 por hora.
            // Arredonda para 2 casas decimais.
            $valor = round($tempoTotal / 60 * 2, 2);

            // Atribui os valores calculados aos dados validados para serem salvos.
            $validatedData['tempo_total'] = $tempoTotal;
            $validatedData['valor'] = $valor;

            // --- Lógica de Negócio: Atualizar Status da Vaga para 'livre' ---
            $vaga = Vaga::find($estacionamento->vaga_id);
            if ($vaga) {
                $vaga->update(['status' => 'livre']);
            }
        }
        // Se a saída já foi registrada ou não foi fornecida, os campos de tempo/valor não são recalculados aqui.
        // Outras atualizações de campo (como vaga_id ou veiculo_id, se permitido) ocorrerão normalmente.

        // Atualiza o registro de estacionamento no banco de dados com os dados validados e calculados.
        $estacionamento->update($validatedData);

        // Carrega novamente os relacionamentos vaga e veiculo para a resposta,
        // garantindo que o status da vaga seja atualizado no JSON de retorno.
        $estacionamento->load(['vaga', 'veiculo']);

        // Retorna o registro de estacionamento atualizado, formatado pelo Resource.
        return new EstacionamentoResource($estacionamento);
    }

    /**
     * Remove the specified resource from storage.
     * Remove o recurso (registro de Estacionamento) especificado do banco de dados.
     *
     * @param  \App\Models\Estacionamento  $estacionamento
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Estacionamento $estacionamento)
    {
        // A lógica abaixo presume que deletar o registro DELETA o histórico e NÃO afeta a vaga.
        // Adapte conforme a regra de negócio do seu projeto.
        if (is_null($estacionamento->saida)) { // Se o veículo ainda está estacionado
            $vaga = Vaga::find($estacionamento->vaga_id);
            if ($vaga) {
                $vaga->update(['status' => 'livre']); // Libera a vaga
            }
        }

        $estacionamento->delete(); // Exclui o registro do banco de dados.

        // Retorna uma resposta vazia com status HTTP 204 No Content,
        // indicando que a operação foi bem-sucedida, mas não há conteúdo para retornar.
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}