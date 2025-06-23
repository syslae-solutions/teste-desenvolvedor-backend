import React from "react";

export default function VeiculoTable({ veiculos, onEdit, onDelete }) {
  if (!veiculos || veiculos.length === 0) {
    return (
      <div className="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded shadow">
        Nenhum veículo encontrado. Crie um novo veículo para começar!
      </div>
    );
  }

  return (
    <div className="overflow-x-auto mt-4 rounded shadow">
      <table className="min-w-full bg-white border border-gray-200">
        <thead>
          <tr className="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
            <th className="py-3 px-6 text-left">Placa</th>
            <th className="py-3 px-6 text-left">Modelo</th>
            <th className="py-3 px-6 text-left">Cor</th>
            <th className="py-3 px-6 text-left">Tipo</th>
            <th className="py-3 px-6 text-center">Ações</th>
          </tr>
        </thead>
        <tbody className="text-gray-600 text-sm font-light">
          {veiculos.map((veiculo) => (
            <tr key={veiculo.id} className="border-b border-gray-200 hover:bg-gray-100">
              <td className="py-3 px-6 text-left whitespace-nowrap">{veiculo.placa}</td>
              <td className="py-3 px-6 text-left">{veiculo.modelo}</td>
              <td className="py-3 px-6 text-left">{veiculo.cor}</td>
              <td className="py-3 px-6 text-left capitalize">
                {veiculo.tipo}
              </td>
              <td className="py-3 px-6 text-center space-x-2">
                <button
                  className="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 text-white rounded shadow-sm transition duration-150 ease-in-out"
                  onClick={() => onEdit(veiculo)}
                >
                  Editar
                </button>
                <button
                  className="bg-red-600 hover:bg-red-700 px-3 py-1 text-white rounded shadow-sm transition duration-150 ease-in-out"
                  onClick={() => onDelete(veiculo.id)}
                >
                  Excluir
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}