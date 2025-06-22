<?php

namespace App\Http\Controllers;

use App\Models\Estacionamento;
use App\Models\Vaga;
use Illuminate\Http\Request;
use Carbon\Carbon; // Para manipulação de datas
use Barryvdh\DomPDF\Facade\Pdf; // Para gerar PDF

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Vagas ocupadas em tempo real
        $vagasOcupadas = Vaga::where('status', 'ocupada')->count();
        $totalVagas = Vaga::count();
        $percentualOcupacao = $totalVagas > 0 ? ($vagasOcupadas / $totalVagas) * 100 : 0;

        // 2. Total de veículos por dia (para o dia atual)
        $totalVeiculosHoje = Estacionamento::whereDate('entrada', Carbon::today())->count();
        // Você pode estender isso para um período se quiser, mas para o dashboard simples, o dia atual é um bom começo.

        // 3. Receita gerada por período (ex: hoje, esta semana, este mês)
        $receitaHoje = Estacionamento::whereDate('entrada', Carbon::today())
                                    ->whereNotNull('saida') // Apenas estacionamentos finalizados
                                    ->sum('valor'); // Assumindo que você tem um campo 'valor_total'

        $receitaSemana = Estacionamento::whereBetween('entrada', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                                      ->whereNotNull('saida')
                                      ->sum('valor');

        $receitaMes = Estacionamento::whereBetween('entrada', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                                    ->whereNotNull('saida')
                                    ->sum('valor');

        return view('dashboard', compact(
            'vagasOcupadas',
            'totalVagas',
            'percentualOcupacao',
            'totalVeiculosHoje',
            'receitaHoje',
            'receitaSemana',
            'receitaMes'
        ));
    }

    // Método para gerar PDF do histórico de estacionamento
    public function generateParkingHistoryPdf(Request $request)
    {
        // Últimos 100 estacionamentos, ou filtrar por período
        $historico = Estacionamento::with(['veiculo', 'vaga']) // Carrega relacionamentos para exibir no PDF
                                 ->orderBy('entrada', 'desc')
                                 ->limit(100)
                                 ->get();

        // Passa os dados para uma view Blade que será usada para gerar o PDF
        $pdf = Pdf::loadView('pdf.parking_history', compact('historico'));

        // Retorna o PDF para download ou exibe no navegador
        return $pdf->download('historico_estacionamento.pdf');
        // Ou para visualizar no navegador: return $pdf->stream('historico_estacionamento.pdf');
    }
}