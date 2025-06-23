import React from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { api } from '../services/api';

export default function NavBar() {
  const { logout, isAuthenticated } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  const handleLogout = async () => {
    try {
      if (isAuthenticated && localStorage.getItem('token') !== 'dummy-token-for-initial-test') {
        await api.post('/logout');
      }
      logout();
      navigate('/login');
    } catch (error) {
      alert('Erro ao fazer logout.');
      console.error('Erro de logout:', error.response || error);
      logout();
      navigate('/login');
    }
  };

  return (
    <nav className="bg-gradient-to-r from-blue-700 to-blue-900 p-4 text-white shadow-lg fixed w-full top-0 z-10"> {/* Design mais atraente */}
      <div className="container mx-auto flex justify-between items-center">
        {/* Lado Esquerdo: Título e Botões de Navegação */}
        <div className="flex items-center space-x-4">
          {/* Botão Voltar */}
          {location.pathname !== '/dashboard' && (
            <button
              onClick={() => navigate(-1)}
              className="p-2 rounded-full hover:bg-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-300"
              title="Voltar"
            >
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </button>
          )}

          {/* Botão Avançar - Visível apenas se houver histórico para avançar (simplificado) */}
          <button
            onClick={() => navigate(1)}
            className="p-2 rounded-full hover:bg-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-300"
            title="Avançar"
          >
            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>

     
        </div>

        {/* Lado Direito: Links de Navegação e Botão de Logout */}
        <div className="flex items-center space-x-6">
          <Link to="/dashboard" className="hover:text-blue-300 transition-colors text-lg font-medium">Dashboard</Link>
          <Link to="/vagas" className="hover:text-blue-300 transition-colors text-lg font-medium">Vagas</Link>
          <Link to="/veiculos" className="hover:text-blue-300 transition-colors text-lg font-medium">Veículos</Link>
          <Link to="/estacionamento" className="hover:text-blue-300 transition-colors text-lg font-medium">Estacionamento</Link>
          
          {/* Botão de Logout */}
          {isAuthenticated && (
            <button
              onClick={handleLogout}
              className="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-full text-md font-semibold transition-colors shadow-md transform hover:scale-105"
            >
              Logout
            </button>
          )}
        </div>
      </div>
    </nav>
  );
}
