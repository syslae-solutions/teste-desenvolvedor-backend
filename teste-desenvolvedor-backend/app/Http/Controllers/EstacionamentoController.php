<?php

namespace App\Http\Controllers;

use App\Models\Estacionamento;
use App\Models\Vaga;
use App\Http\Requests\EstacionamentoEntradaRequest;
use App\Http\Requests\EstacionamentoSaidaRequest;
use App\Http\Resources\EstacionamentoResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Importar Facade do DomPDF

class EstacionamentoController extends Controller
{
    /**
     * Registra a entrada de um veículo em uma vaga.
     * @param EstacionamentoEntradaRequest $request
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\EstacionamentoResource
     */
    public function entrada(EstacionamentoEntradaRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $vaga = Vaga::find($request->vaga_id);

            // Regra: Não permitir entrada se a vaga estiver ocupada ou interditada (já validado no FormRequest)
            // Lógica duplicada para clareza, mas a validação no FormRequest é a que 'garante'
            if ($vaga->status === 'ocupada' || $vaga->status === 'interditada') {
                return response()->json(['message' => 'A vaga não está disponível.'], 409);
            }

            $estacionamento = Estacionamento::create([
                'vaga_id' => $request->vaga_id,
                'veiculo_id' => $request->veiculo_id,
                'entrada_at' => now(),
            ]);

            $vaga->status = 'ocupada';
            $vaga->save();

            return new EstacionamentoResource($estacionamento->load(['vaga', 'veiculo']));
        });
    }

    /**
     * Registra a saída de um veículo e calcula o valor.
     * @param EstacionamentoSaidaRequest $request
     * @return \Illuminate\Http\JsonResponse|\App\Http\Resources\EstacionamentoResource
     */
    public function saida(EstacionamentoSaidaRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $estacionamento = Estacionamento::with('vaga')
                                            ->find($request->estacionamento_id);

            // Regra: Não permitir saída se o veículo não estiver estacionado (já validado no FormRequest)
            // Lógica duplicada para clareza, mas a validação no FormRequest é a que 'garante'
            if (!$estacionamento || $estacionamento->saida_at !== null) {
                return response()->json(['message' => 'Operação de estacionamento inválida ou já finalizada.'], 400);
            }

            $estacionamento->saida_at = now();

            // Calcular tempo e valor (Ex: R$ 2,00/hora, fracionado)
            $entrada = $estacionamento->entrada_at;
            $saida = $estacionamento->saida_at;

            $diffInMinutes = $saida->diffInMinutes($entrada);
            // Cada hora (ou fração) custa R$ 2,00. Ex: 61 minutos = 2 horas de cobrança.
            $hours = ceil($diffInMinutes / 60);
            $valorPorHora = 2.00;
            $valorTotal = $hours * $valorPorHora;

            $estacionamento->valor = $valorTotal;
            $estacionamento->save();

            $estacionamento->vaga->status = 'livre'; // Libera a vaga
            $estacionamento->vaga->save();

            return new EstacionamentoResource($estacionamento->load(['vaga', 'veiculo']));
        });
    }

    /**
     * Exibe o histórico de estacionamentos.
     * Pode ser usado para listar operações ativas (saida_at IS NULL) ou histórico completo.
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Estacionamento::query()->with(['vaga', 'veiculo']);

        // Filtro para operações ativas (sem saída registrada)
        if ($request->boolean('ativo')) {
            $query->whereNull('saida_at');
        }

        // Exemplo de filtro por veículo ou vaga
        if ($request->has('veiculo_id')) {
            $query->where('veiculo_id', $request->input('veiculo_id'));
        }
        if ($request->has('vaga_id')) {
            $query->where('vaga_id', $request->input('vaga_id'));
        }
        if ($request->has('placa')) {
            $query->whereHas('veiculo', function ($q) use ($request) {
                $q->where('placa', 'like', '%' . $request->input('placa') . '%');
            });
        }
        if ($request->has('codigo_vaga')) {
            $query->whereHas('vaga', function ($q) use ($request) {
                $q->where('codigo', 'like', '%' . $request->input('codigo_vaga') . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        $estacionamentos = $query->latest('entrada_at')->paginate($perPage);

        return EstacionamentoResource::collection($estacionamentos);
    }

    /**
     * Gera e faz o download de um PDF do histórico de estacionamento.
     * GET /api/estacionamento/historico/pdf
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadHistoricoPdf(Request $request)
    {
        $dataInicial = $request->input('data_inicial', now()->subDays(30)->toDateString());
        $dataFinal = $request->input('data_final', now()->toDateString());

        $historico = Estacionamento::with(['veiculo', 'vaga'])
                                    ->whereBetween(DB::raw('DATE(entrada_at)'), [$dataInicial, $dataFinal])
                                    ->orderBy('entrada_at', 'desc')
                                    ->get();

        // Carregar a view Blade para o PDF com os dados do histórico
        $pdf = Pdf::loadView('pdf.historico', compact('historico', 'dataInicial', 'dataFinal'));

        // Retornar o PDF para download
        return $pdf->download('historico_estacionamento_' . now()->format('Ymd_His') . '.pdf');
    }
}
