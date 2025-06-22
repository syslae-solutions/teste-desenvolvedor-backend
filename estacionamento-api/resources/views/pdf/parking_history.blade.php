<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Estacionamento</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; } /* Usar Dejavu Sans para suportar caracteres especiais no PDF */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 9px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Histórico de Estacionamento</h1>
        <p>Gerado em: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Veículo (Placa)</th>
                <th>Vaga (Cód.)</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Duração</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($historico as $registro)
                <tr>
                    <td>{{ $registro->id }}</td>
                    <td>{{ $registro->veiculo->placa ?? 'N/A' }} ({{ $registro->veiculo->modelo ?? 'N/A' }})</td>
                    <td>{{ $registro->vaga->codigo ?? 'N/A' }} ({{ $registro->vaga->localizacao_rua ?? 'N/A' }})</td>
                    <td>{{ $registro->data_entrada ? \Carbon\Carbon::parse($registro->data_entrada)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                    <td>{{ $registro->data_saida ? \Carbon\Carbon::parse($registro->data_saida)->format('d/m/Y H:i:s') : 'Em andamento' }}</td>
                    <td>
                        @if ($registro->data_entrada && $registro->data_saida)
                            {{ \Carbon\Carbon::parse($registro->data_saida)->diffForHumans(\Carbon\Carbon::parse($registro->data_entrada), true) }}
                        @else
                            Em andamento
                        @endif
                    </td>
                    <td>R$ {{ number_format($registro->valor_total ?? 0, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhum registro de estacionamento encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Relatório de Estacionamento - {{ config('app.name') }}</p>
    </div>
</body>
</html>