<?php

namespace App\Http\Controllers;

use App\Models\Estacionamento;
use App\Models\Vaga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retorna o número de vagas ocupadas em tempo real.
     * GET /api/dashboard/vagas-ocupadas
     * @return \Illuminate\Http\JsonResponse
     */
    public function vagasOcupadas(): JsonResponse
    {
        $ocupadas = Vaga::where('status', 'ocupada')->count();
        $total = Vaga::count();

        return response()->json([
            'ocupadas' => $ocupadas,
            'total' => $total,
            'percentual' => $total > 0 ? round(($ocupadas / $total) * 100, 2) : 0,
        ]);
    }

    /**
     * Retorna o total de veículos por dia.
     * GET /api/dashboard/veiculos-por-dia
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function veiculosPorDia(Request $request): JsonResponse
    {
        $dataInicial = $request->input('data_inicial', now()->subDays(7)->toDateString());
        $dataFinal = $request->input('data_final', now()->toDateString());

        $veiculos = Estacionamento::select(
                DB::raw('DATE(entrada_at) as data'),
                DB::raw('COUNT(DISTINCT veiculo_id) as total_veiculos')
            )
            ->whereBetween(DB::raw('DATE(entrada_at)'), [$dataInicial, $dataFinal])
            ->groupBy('data')
            ->orderBy('data', 'asc')
            ->get();

        return response()->json($veiculos);
    }

    /**
     * Retorna a receita gerada por período.
     * GET /api/dashboard/receita-por-periodo
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function receitaPorPeriodo(Request $request): JsonResponse
    {
        $dataInicial = $request->input('data_inicial', now()->subDays(30)->toDateString());
        $dataFinal = $request->input('data_final', now()->toDateString());

        $receitaTotal = Estacionamento::whereNotNull('valor')
                                    ->whereBetween(DB::raw('DATE(saida_at)'), [$dataInicial, $dataFinal])
                                    ->sum('valor');

        return response()->json([
            'receita_total' => round($receitaTotal, 2),
            'data_inicial' => $dataInicial,
            'data_final' => $dataFinal,
        ]);
    }
}
