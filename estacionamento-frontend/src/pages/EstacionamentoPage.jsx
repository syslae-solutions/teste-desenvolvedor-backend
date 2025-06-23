import React, { useEffect, useState } from "react";
import { api } from "../services/api";
import Select from 'react-select'; // Para selects mais amigáveis

export default function EstacionamentoPage() {
  const [vagasAtivas, setVagasAtivas] = useState([]);
  const [vagasDisponiveis, setVagasDisponiveis] = useState([]);
  const [veiculosDisponiveis, setVeiculosDisponiveis] = useState([]);
  
  const [vagaSelecionadaEntrada, setVagaSelecionadaEntrada] = useState(null);
  const [veiculoSelecionadoEntrada, setVeiculoSelecionadoEntrada] = useState(null);

  const carregarDados = async () => {
    try {
      // Carregar vagas ativas (ocupadas) para a tabela de estacionamentos
      const resVagasAtivas = await api.get("/estacionamento/historico?ativo=true");
      setVagasAtivas(resVagasAtivas.data.data || resVagasAtivas.data);

      // Carregar vagas livres para entrada
      const resVagasLivres = await api.get("/vagas?status=livre");
      setVagasDisponiveis(resVagasLivres.data.data.map(v => ({ value: v.id, label: `${v.codigo} (${v.localizacao})` })) || []);

      // Carregar veículos que não estão estacionados (sem saida_at)
      const resVeiculosLivres = await api.get("/veiculos"); // Por simplicidade, pegamos todos e filtramos no front
      // Filtrar veículos que não estão atualmente estacionados
      const veiculosNaoEstacionados = resVeiculosLivres.data.data.filter(
        v => !resVagasAtivas.data.data.some(op => op.veiculo_id === v.id)
      ).map(v => ({ value: v.id, label: `${v.placa} - ${v.modelo}` }));
      setVeiculosDisponiveis(veiculosNaoEstacionados);

    } catch (err) {
      alert("Erro ao carregar dados da página de Estacionamento.");
      console.error("Erro ao carregar dados:", err.response || err);
    }
  };

  useEffect(() => {
    carregarDados();
  }, []); 

  const handleEntrada = async () => {
    if (!vagaSelecionadaEntrada || !veiculoSelecionadoEntrada) {
      alert("Selecione uma vaga e um veículo para registrar a entrada.");
      return;
    }
    try {
      await api.post("/estacionamento/entrada", {
        vaga_id: vagaSelecionadaEntrada.value,
        veiculo_id: veiculoSelecionadoEntrada.value,
      });
      alert("Entrada registrada com sucesso!");
      setVagaSelecionadaEntrada(null);
      setVeiculoSelecionadoEntrada(null);
      carregarDados(); // Recarrega os dados para atualizar listas
    } catch (err) {
      alert("Erro ao registrar entrada. Verifique as regras (vaga/veículo).");
      console.error("Erro na entrada:", err.response || err);
    }
  };

  const handleSaida = async (estacionamentoId) => {
    if (confirm("Tem certeza que deseja registrar a saída deste veículo?")) {
      try {
        await api.post("/estacionamento/saida", { estacionamento_id: estacionamentoId });
        alert("Saída registrada com sucesso! Valor calculado.");
        carregarDados(); // Recarrega os dados para atualizar listas
      } catch (err) {
        alert("Erro ao registrar saída.");
        console.error("Erro na saída:", err.response || err);
      }
    }
  };

  return (
    <div className="p-4">
      <h1 className="text-3xl font-bold mb-6 text-gray-800">Operações de Estacionamento</h1> {/* Título mais proeminente */}

      {/* Navegação Rápida - REMOVIDO, AGORA NA NAVBAR */}

      {/* Registrar Entrada */}
      <div className="bg-white p-6 rounded-xl shadow-lg mb-6 border border-blue-200"> {/* Estilizado */}
        <h2 className="text-xl font-semibold mb-4 text-gray-700">Registrar Entrada de Veículo</h2>
        <div className="mb-4">
          <label className="block text-sm font-medium text-gray-700 mb-1">Vaga Disponível:</label>
          <Select
            options={vagasDisponiveis}
            value={vagaSelecionadaEntrada}
            onChange={setVagaSelecionadaEntrada}
            placeholder="Selecione uma vaga"
            className="w-full text-gray-800"
            isClearable
            styles={{ control: (base) => ({ ...base, borderRadius: '0.5rem', borderColor: '#d1d5db' }) }} // Tailwind-like rounded border
          />
        </div>
        <div className="mb-4">
          <label className="block text-sm font-medium text-gray-700 mb-1">Veículo:</label>
          <Select
            options={veiculosDisponiveis}
            value={veiculoSelecionadoEntrada}
            onChange={setVeiculoSelecionadoEntrada}
            placeholder="Selecione um veículo"
            className="w-full text-gray-800"
            isClearable
            styles={{ control: (base) => ({ ...base, borderRadius: '0.5rem', borderColor: '#d1d5db' }) }} // Tailwind-like rounded border
          />
        </div>
        <button
          className="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg shadow-md font-semibold transform hover:scale-105 transition duration-300" // Estilizado
          onClick={handleEntrada}
        >
          Registrar Entrada
        </button>
      </div>

      {/* Vagas Ativas (Estacionamentos Ativos) */}
      <div className="bg-white p-6 rounded-xl shadow-lg"> {/* Estilizado */}
        <h2 className="text-xl font-semibold mb-4 text-gray-700">Estacionamentos Ativos</h2>
        {vagasAtivas.length === 0 ? (
          <p className="text-gray-600 p-4 bg-gray-50 rounded-lg">Nenhum veículo estacionado no momento.</p>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
              <thead>
                <tr className="bg-blue-100 text-blue-800 uppercase text-sm leading-normal"> {/* Estilizado */}
                  <th className="py-3 px-6 text-left">Vaga</th>
                  <th className="py-3 px-6 text-left">Veículo</th>
                  <th className="py-3 px-6 text-left">Entrada</th>
                  <th className="py-3 px-6 text-center">Ações</th>
                </tr>
              </thead>
              <tbody className="text-gray-700 text-sm font-light">
                {vagasAtivas.map((operacao) => (
                  <tr key={operacao.id} className="border-b border-gray-200 hover:bg-blue-50 transition-colors"> {/* Estilizado */}
                    <td className="py-3 px-6 text-left">{operacao.vaga?.codigo} ({operacao.vaga?.localizacao})</td>
                    <td className="py-3 px-6 text-left">{operacao.veiculo?.placa} - {operacao.veiculo?.modelo}</td>
                    <td className="py-3 px-6 text-left">{new Date(operacao.entrada_at).toLocaleString()}</td>
                    <td className="py-3 px-6 text-center">
                      <button
                        className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-sm font-semibold transform hover:scale-105 transition duration-300" // Estilizado
                        onClick={() => handleSaida(operacao.id)}
                      >
                        Registrar Saída
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
