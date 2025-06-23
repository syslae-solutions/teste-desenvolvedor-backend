import React, { useEffect, useState } from "react";
import { api } from "../services/api";
import VagaForm from "../components/VagaForm";
import VagaTable from "../components/VagaTable";
import { data } from "autoprefixer";

export default function VagasPage() {
  const [vagas, setVagas] = useState([]);
  const [vagaSelecionada, setVagaSelecionada] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [loading, setLoading] = useState(true); // Add loading state
  const [error, setError] = useState(null); // Add error state

  
 const carregarVagas = async () => {
    setLoading(true); // Set loading to true before fetching
    setError(null); // Clear previous errors
    try {
      const response = await api.get("/vagas");
      setVagas(response.data.data || response.data); 
    } catch (err) {
      console.error("Erro ao carregar vagas:", err.response || err);
      setError("Não foi possível carregar as vagas. Tente novamente."); // User-friendly error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or failure)
    }
  };

  const handleDelete = async (id) => {
    if (confirm("Tem certeza que deseja excluir essa vaga?")) {
      try {
        await api.delete('/vagas/${id}');
        carregarVagas();
      } catch (err) {
        alert("Erro ao excluir vaga");
        console.error("Erro ao excluir vaga:", err.response || err);
      }
    }
  };

  const handleEdit = (vaga) => {
    setVagaSelecionada(vaga);
    setShowForm(true);
  };

  const handleNew = () => {
    setVagaSelecionada(null); // Limpa a vaga selecionada para um novo formulário
    setShowForm(true);
  };

  const handleSave = () => {
    setShowForm(false);
    setVagaSelecionada(null); // Reseta a vaga selecionada
    carregarVagas(); // Recarrega a lista de vagas
  };

  // Carrega as vagas na montagem do componente
  useEffect(() => {
    carregarVagas();
  }, []);
return (
    <div className="min-h-screen bg-gray-100 flex itens-center justify-center p-6">
      <div className="w-full max-w-4xl bg-white shadow-lg rounded-lg p-6">
        <h1 className="text-4xl font-extrabold text-gray-800 mb-6 text-center">
          Gerenciamento de Vagas
        </h1>


        {/* Renderiza o formulário se showForm for true */}
        {showForm && (
          <div className="mt-8 p-6 bg-white border border-gray-200 rounded-lg shadow-md">
            <h2 className="text-2xl font-semibold text-gray-700 mb-5">
              {vagaSelecionada ? "Editar Vaga" : "Cadastrar Nova Vaga"}
            </h2>
            <VagaForm
              vagaEditando={vagaSelecionada}
              onClose={() => setShowForm(false)}
              onSave={handleSave}
            />
          </div>
        )}

        {/* Display loading, error, or table */}
        {loading ? (
          <div className="flex justify-center items-center h-48">
            <div className="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500"></div>
            <p className="ml-4 text-gray-600 text-lg">Carregando vagas...</p>
          </div>
        ) : error ? (
          <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong className="font-bold">Erro:</strong>
            <span className="block sm:inline ml-2">{error}</span>
          </div>
        ) : vagas.length === 0 && !showForm ? (
          <div className="text-center py-10 px-4 bg-gray-50 rounded-lg border border-gray-200 shadow-sm">
            <p className="text-xl text-gray-600 font-medium mb-2">
              Nenhuma vaga encontrada.
            </p>
            <p className="text-gray-500">
              Clique em "Nova Vaga" para começar a cadastrar.
            </p>
          </div>
        ) : (
          /* Renderiza a tabela de vagas */
          <div className="mt-8">
            <h2 className="text-2xl font-semibold text-gray-700 mb-4">
              Vagas Cadastradas
            </h2>
            <VagaTable vagas={vagas} onEdit={handleEdit} onDelete={handleDelete} />
          </div>
        )}
        <div className="flex justify-end mb-6">
          <button
            className="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md shadow-md transition duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75"
            onClick={handleNew}
          >
            + Nova Vaga
          </button>
        </div>
      </div>
    </div>
  );
}