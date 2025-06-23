import React, { useEffect, useState } from "react";
import { api } from "../services/api";
import {
  LineChart,
  Line,
  CartesianGrid,
  XAxis,
  YAxis,
  Tooltip,
  ResponsiveContainer // Importado para gráficos responsivos
} from "recharts";

export default function DashboardPage() {
  const [vagasOcupadas, setVagasOcupadas] = useState({ ocupadas: 0, total: 0, percentual: 0 });
  const [veiculosPorDia, setVeiculosPorDia] = useState([]);
  const [receitaPorPeriodo, setReceitaPorPeriodo] = useState({ receita_total: 0, data_inicial: "", data_final: "" });

  useEffect(() => {
    const carregarDadosDashboard = async () => {
      try {
        // Requisição para vagas ocupadas
        const resVagas = await api.get("/dashboard/vagas-ocupadas");
        setVagasOcupadas(resVagas.data);

        // Requisição para veículos por dia (últimos 7 dias, exemplo)
        const dataFinal = new Date().toISOString().split('T')[0];
        const dataInicial = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        const resVeiculos = await api.get("/dashboard/veiculos-por-dia", {
          params: { data_inicial: dataInicial, data_final: dataFinal },
        });
        // Formata os dados para o Recharts
        const formattedVeiculos = resVeiculos.data.map(item => ({
          name: item.data, // Data como nome do eixo X
          "Total de Veículos": item.total_veiculos, // Valor para o gráfico
        }));
        setVeiculosPorDia(formattedVeiculos);

        // Requisição para receita por período (últimos 30 dias, exemplo)
        const resReceita = await api.get("/dashboard/receita-por-periodo", {
          params: {
            data_inicial: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            data_final: new Date().toISOString().split('T')[0],
          },
        });
        setReceitaPorPeriodo(resReceita.data);

      } catch (err) {
        alert("Erro ao carregar dados do Dashboard.");
        console.error("Erro no Dashboard:", err.response || err);
      }
    };

    carregarDadosDashboard();
  }, []);

  const handleGeneratePdf = async () => {
    try {
      const dataFinal = new Date().toISOString().split('T')[0];
      const dataInicial = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
      // A requisição retorna um blob (ficheiro), por isso responseType: 'blob'
      const response = await api.get("/estacionamento/historico/pdf", {
        responseType: 'blob', // Importante para receber o PDF
        params: { data_inicial: dataInicial, data_final: dataFinal },
      });

      // Cria um URL para o blob e simula um clique para download
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `historico_estacionamento_${new Date().toISOString().split('T')[0]}.pdf`);
      document.body.appendChild(link);
      link.click();
      link.parentNode.removeChild(link);
      window.URL.revokeObjectURL(url); // Libera o URL do objeto
      alert("PDF gerado com sucesso!");
    } catch (err) {
      alert("Erro ao gerar PDF.");
      console.error("Erro ao gerar PDF:", err.response || err);
    }
  };

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold mb-6 text-gray-800">Dashboard de Estacionamento</h1>

      {/* Cartões de métricas */}
      <div className="flex flex-wrap gap-4 mb-6"> {/* Removido bottom-10 */}
        <div className="flex-1 min-w-[200px] bg-white p-4 rounded-xl shadow text-center transform hover:scale-105 transition duration-300">
          <p className="text-gray-500 text-sm">Vagas Ocupadas Agora</p>
          <p className="text-3xl font-semibold mt-2 text-blue-600">{vagasOcupadas.ocupadas} / {vagasOcupadas.total}</p>
          <p className="text-gray-600 text-sm">{vagasOcupadas.percentual}% ocupadas</p>
        </div>
        <div className="flex-1 min-w-[200px] bg-white p-4 rounded-xl shadow text-center transform hover:scale-105 transition duration-300">
          <p className="text-gray-500 text-sm">Total de Veículos (últ. 7 dias)</p>
          <p className="text-3xl font-semibold mt-2 text-purple-600">
            {veiculosPorDia.reduce((sum, item) => sum + item["Total de Veículos"], 0)}
          </p>
          <p className="text-gray-600 text-sm">registrados</p>
        </div>
        <div className="flex-1 min-w-[200px] bg-white p-4 rounded-xl shadow text-center transform hover:scale-105 transition duration-300">
          <p className="text-gray-500 text-sm">Receita Total (últ. 30 dias)</p>
          <p className="text-3xl font-semibold mt-2 text-green-600">
            R$ {receitaPorPeriodo.receita_total ? receitaPorPeriodo.receita_total.toFixed(2).replace('.', ',') : '0,00'}
          </p>
          <p className="text-gray-600 text-sm">gerada</p>
        </div>
        <div className="flex-1 min-w-[200px] bg-white p-4 rounded-xl shadow text-center flex flex-col justify-between transform hover:scale-105 transition duration-300">
          <div>
            <p className="text-gray-500 text-sm mb-2">Histórico de Estacionamento</p>
          </div>
          <button
            className="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded shadow-md w-full"
            onClick={handleGeneratePdf}
          >
            Gerar PDF
          </button>
        </div>
      </div>

      {/* Gráfico */}
      <div className="bg-white p-4 rounded-xl shadow">
        <h2 className="text-xl font-semibold mb-4 text-gray-800">Veículos Registrados por Dia</h2>
        <ResponsiveContainer width="100%" height={300}>
          <LineChart data={veiculosPorDia} margin={{ top: 5, right: 20, left: 10, bottom: 5 }}>
            <CartesianGrid strokeDasharray="3 3" stroke="#e0e0e0" />
            <XAxis dataKey="name" tickFormatter={(tick) => {
              const date = new Date(tick);
              return `${date.getDate()}/${date.getMonth() + 1}`; // Formata como DD/MM
            }} />
            <YAxis />
            <Tooltip formatter={(value) => [`${value} veículos`, 'Total de Veículos']} />
            <Line
              type="monotone"
              dataKey="Total de Veículos"
              stroke="#8884d8"
              strokeWidth={2}
              activeDot={{ r: 8 }}
            />
          </LineChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}
