<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Estacionamento</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Dashboard de Estacionamento</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-blue-100 p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-blue-800 mb-2">Status das Vagas</h2>
                <p class="text-gray-700">Vagas Ocupadas: <span class="font-bold text-2xl">{{ $vagasOcupadas }}</span> / {{ $totalVagas }}</p>
                <p class="text-gray-700">Percentual de Ocupação: <span class="font-bold text-2xl text-blue-600">{{ number_format($percentualOcupacao, 2) }}%</span></p>
            </div>

            <div class="bg-green-100 p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-green-800 mb-2">Movimentação Diária</h2>
                <p class="text-gray-700">Total de Veículos Estacionados Hoje: <span class="font-bold text-2xl">{{ $totalVeiculosHoje }}</span></p>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Receita Gerada</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-yellow-100 p-4 rounded-lg shadow-md text-center">
                    <p class="text-gray-700 text-lg">Hoje</p>
                    <p class="text-yellow-700 font-bold text-3xl">R$ {{ number_format($receitaHoje, 2, ',', '.') }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg shadow-md text-center">
                    <p class="text-gray-700 text-lg">Esta Semana</p>
                    <p class="text-yellow-700 font-bold text-3xl">R$ {{ number_format($receitaSemana, 2, ',', '.') }}</p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg shadow-md text-center">
                    <p class="text-gray-700 text-lg">Este Mês</p>
                    <p class="text-yellow-700 font-bold text-3xl">R$ {{ number_format($receitaMes, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Relatórios</h2>
            <a href="{{ route('dashboard.pdf') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-300 ease-in-out">
                Gerar PDF do Histórico de Estacionamento
            </a>
        </div>
    </div>
</body>
</html>