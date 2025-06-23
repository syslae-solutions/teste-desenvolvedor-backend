import React from "react";

export default function VagaTable({ vagas, onEdit, onDelete }) {
  if (!vagas || vagas.length === 0) {
    return (
      <div className="mt-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded shadow">
        Nenhuma vaga encontrada. Crie uma nova vaga para começar!
      </div>
    );
  }

  return (
    <div className="overflow-x-auto mt-4 rounded shadow">
      <table className="min-w-full bg-white border border-gray-200">
        <thead>
          <tr className="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
            <th className="py-3 px-6 text-left">Código</th>
            <th className="py-3 px-6 text-left">Localização</th>
            <th className="py-3 px-6 text-left">Status</th>
            <th className="py-3 px-6 text-center">Ações</th>
          </tr>
        </thead>
        <tbody className="text-gray-600 text-sm font-light">
          {vagas.map((vaga) => (
            <tr key={vaga.id} className="border-b border-gray-200 hover:bg-gray-100">
              <td className="py-3 px-6 text-left whitespace-nowrap">{vaga.codigo}</td>
              <td className="py-3 px-6 text-left">{vaga.localizacao}</td>
              <td className="py-3 px-6 text-left capitalize">
                <span className={`py-1 px-3 rounded-full text-xs ${
                  vaga.status === 'livre' ? 'bg-green-200 text-green-600' :
                  vaga.status === 'ocupada' ? 'bg-red-200 text-red-600' :
                  'bg-gray-200 text-gray-600' // 'interditada' ou outro
                }`}>
                  {vaga.status}
                </span>
              </td>
              <td className="py-3 px-6 text-center space-x-2">
                <button
                  className="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 text-white rounded shadow-sm transition duration-150 ease-in-out"
                  onClick={() => onEdit(vaga)}
                >
                  Editar
                </button>
                <button
                  className="bg-red-600 hover:bg-red-700 px-3 py-1 text-white rounded shadow-sm transition duration-150 ease-in-out"
                  onClick={() => onDelete(vaga.id)}
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