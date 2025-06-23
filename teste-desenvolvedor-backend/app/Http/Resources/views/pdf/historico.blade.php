<!DOCTYPE html>
<html>
<head>
    <title>Histórico de Estacionamento</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { font-size: 18px; text-align: center; margin-bottom: 20px; }
        .periodo { text-align: center; margin-bottom: 20px; font-size: 12px; color: #555;}
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #888; }
    </style>
</head>
<body>
    <h1>Histórico de Estacionamento</h1>
    <div class="periodo">Período: {{ $dataInicial }} a {{ $dataFinal }}</div>

    <table>
        <thead>
            <tr>
                <th>Veículo (Placa)</th>
                <th>Vaga (Código)</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Tempo (min)</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($historico as $registro)
                <tr>
                    <td>{{ $registro->veiculo->placa ?? 'N/A' }}</td>
                    <td>{{ $registro->vaga->codigo ?? 'N/A' }}</td>
                    <td>{{ $registro->entrada_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $registro->saida_at ? $registro->saida_at->format('d/m/Y H:i:s') : 'Em Aberto' }}</td>
                    <td>{{ $registro->saida_at ? $registro->saida_at->diffInMinutes($registro->entrada_at) : 'N/A' }}</td>
                    <td>{{ $registro->valor ? number_format($registro->valor, 2, ',', '.') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Gerado em {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>
