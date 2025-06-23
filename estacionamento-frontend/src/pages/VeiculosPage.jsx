import React, { useEffect, useState } from "react";
import { api } from "../services/api";
import VeiculoForm from "../components/VeiculoForm"; // Precisaremos criar este componente
import VeiculoTable from "../components/VeiculoTable"; // Precisaremos criar este componente

export default function VeiculosPage() {
  const [veiculos, setVeiculos] = useState([]);
  const [veiculoSelecionado, setVeiculoSelecionado] = useState(null);
  const [showForm, setShowForm] = useState(false);

  const carregarVeiculos = async () => {
    try {
      const response = await api.get("/veiculos");
      setVeiculos(response.data.data || response.data);
    } catch (err) {
      alert("Erro ao carregar veículos.");
      console.error("Erro ao carregar veículos:", err.response || err);
    }
  };

  const handleDelete = async (id) => {
    if (confirm("Tem certeza que deseja excluir este veículo?")) {
      try {
        await api.delete(`/veiculos/${id}`);
        carregarVeiculos();
      } catch (err) {
        alert("Erro ao excluir veículo.");
        console.error("Erro ao excluir veículo:", err.response || err);
      }
    }
  };

  const handleEdit = (veiculo) => {
    setVeiculoSelecionado(veiculo);
    setShowForm(true);
  };

  const handleNew = () => {
    setVeiculoSelecionado(null);
    setShowForm(true);
  };

  const handleSave = () => {
    setShowForm(false);
    setVeiculoSelecionado(null);
    carregarVeiculos();
  };

  useEffect(() => {
    carregarVeiculos();
  }, []);

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold mb-4">Gerenciamento de Veículos</h1>
      <button
        className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow"
        onClick={handleNew}
      >
        Novo Veículo
      </button>

      {showForm && (
        <VeiculoForm
          veiculoEditando={veiculoSelecionado}
          onClose={() => setShowForm(false)}
          onSave={handleSave}
        />
      )}

      <VeiculoTable
        veiculos={veiculos}
        onEdit={handleEdit}
        onDelete={handleDelete}
      />
    </div>
  );
}