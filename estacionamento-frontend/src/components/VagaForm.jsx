import React, { useState, useEffect } from "react";
import { api } from "../services/api";

export default function VagaForm({ onClose, onSave, vagaEditando }) {
  const [form, setForm] = useState({
    codigo: "",
    localizacao: "", // Contém rua, numero, bairro separados por vírgula
    status: "livre",
  });

  // Preenche o formulário se estiver editando uma vaga existente
  useEffect(() => {
    if (vagaEditando) {
      setForm({
        codigo: vagaEditando.codigo,
        localizacao: vagaEditando.localizacao, // Já vem formatado do Resource
        status: vagaEditando.status,
      });
    } else {
      // Reseta o formulário para uma nova vaga
      setForm({
        codigo: "",
        localizacao: "",
        status: "livre",
      });
    }
  }, [vagaEditando]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (vagaEditando) {
        // Envia PUT para atualizar
        await api.put(`/vagas/${vagaEditando.id}`, form);
      } else {
        // Envia POST para criar
        await api.post("/vagas", form);
      }
      onSave(); // Notifica o componente pai para recarregar vagas e fechar o formulário
    } catch (err) {
      alert("Erro ao salvar vaga. Verifique os dados.");
      console.error("Erro ao salvar vaga:", err.response || err); // Para depuração
    }
  };

  return (
    <div className="p-4 border mt-4 bg-white rounded shadow">
      <h2 className="text-xl font-semibold mb-4">
        {vagaEditando ? "Editar Vaga" : "Nova Vaga"}
      </h2>
      <form onSubmit={handleSubmit}>
        <div className="mb-2">
          <label htmlFor="codigo" className="block text-sm font-medium text-gray-700">Código</label>
          <input
            name="codigo"
            id="codigo"
            placeholder="Ex: VAGA001"
            value={form.codigo}
            onChange={handleChange}
            className="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div className="mb-2">
          <label htmlFor="localizacao" className="block text-sm font-medium text-gray-700">Localização (Rua, Número, Bairro)</label>
          <input
            name="localizacao"
            id="localizacao"
            placeholder="Ex: Rua X, 123, Centro"
            value={form.localizacao}
            onChange={handleChange}
            className="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div className="mb-4">
          <label htmlFor="status" className="block text-sm font-medium text-gray-700">Status</label>
          <select
            name="status"
            id="status"
            value={form.status}
            onChange={handleChange}
            className="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="livre">Livre</option>
            <option value="ocupada">Ocupada</option>
            <option value="interditada">Interditada</option>
          </select>
        </div>
        <button className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
          Salvar
        </button>
        <button
          type="button"
          onClick={onClose}
          className="ml-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded shadow"
        >
          Cancelar
        </button>
      </form>
    </div>
  );
}
