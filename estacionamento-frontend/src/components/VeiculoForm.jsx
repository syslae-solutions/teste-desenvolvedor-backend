import React, { useState, useEffect } from "react";
import { api } from "../services/api";

export default function VeiculoForm({ onClose, onSave, veiculoEditando }) {
  const [form, setForm] = useState({
    placa: "",
    modelo: "",
    cor: "",
    tipo: "carro", // Padrão
  });

  useEffect(() => {
    if (veiculoEditando) {
      setForm({
        placa: veiculoEditando.placa,
        modelo: veiculoEditando.modelo,
        cor: veiculoEditando.cor,
        tipo: veiculoEditando.tipo,
      });
    } else {
      setForm({
        placa: "",
        modelo: "",
        cor: "",
        tipo: "carro",
      });
    }
  }, [veiculoEditando]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (veiculoEditando) {
        await api.put(`/veiculos/${veiculoEditando.id}`, form);
      } else {
        await api.post("/veiculos", form);
      }
      onSave();
    } catch (err) {
      alert("Erro ao salvar veículo. Verifique os dados e a placa.");
      console.error("Erro ao salvar veículo:", err.response || err);
    }
  };

  return (
    <div className="p-4 border mt-4 bg-white rounded shadow">
      <h2 className="text-xl font-semibold mb-4">
        {veiculoEditando ? "Editar Veículo" : "Novo Veículo"}
      </h2>
      <form onSubmit={handleSubmit}>
        <div className="mb-2">
          <label htmlFor="placa" className="block text-sm font-medium text-gray-700">Placa (Mercosul)</label>
          <input
            name="placa"
            id="placa"
            placeholder="Ex: ABC1D23"
            value={form.placa}
            onChange={handleChange}
            className="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            maxLength={7} // Placa Mercosul sem o traço, se o backend espera assim
            pattern="[A-Za-z]{3}[0-9][A-Za-z0-9][0-9]{2}" // Regex de validação básica no frontend
          />
        </div>
        <div className="mb-2">
          <label htmlFor="modelo" className="block text-sm font-medium text-gray-700">Modelo</label>
          <input
            name="modelo"
            id="modelo"
            placeholder="Ex: Fiat Palio"
            value={form.modelo}
            onChange={handleChange}
            className="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div className="mb-2">
          <label htmlFor="cor" className="block text-sm font-medium text-gray-700">Cor</label>
          <input
            name="cor"
            id="cor"
            placeholder="Ex: Prata"
            value={form.cor}
            onChange={handleChange}
            className="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>
        <div className="mb-4">
          <label htmlFor="tipo" className="block text-sm font-medium text-gray-700">Tipo</label>
          <select
            name="tipo"
            id="tipo"
            value={form.tipo}
            onChange={handleChange}
            className="border p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="carro">Carro</option>
            <option value="moto">Moto</option>
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