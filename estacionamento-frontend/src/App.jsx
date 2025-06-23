import React from "react";
import { Route, Routes, Navigate } from "react-router-dom";
import LoginPage from "./pages/LoginPage";
import VagasPage from "./pages/VagasPage";
import VeiculosPage from "./pages/VeiculosPage";
import EstacionamentoPage from "./pages/EstacionamentoPage";
import DashboardPage from "./pages/DashboardPage";
import NavBar from "./components/NavBar";
import { AuthProvider, useAuth } from "./context/AuthContext";

function PrivateRoute({ children }) {
  const { isAuthenticated } = useAuth();
  return isAuthenticated ? children : <Navigate to="/login" />;
}

function App() {
  return (
    <AuthProvider>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        {/* Rotas protegidas que terão o NavBar */}
        <Route
          path="*" // Usa o curinga para rotas que não são /login
          element={
            <PrivateRoute>
              <NavBar /> {/* NavBar renderizado aqui */}
              {/* O conteúdo da página será um filho da rota */}
              {/* Adicionado padding-top maior (pt-24) para compensar a navbar fixa e espaçar */}
              <div className="pt-24 px-4"> {/* Adicionado px-4 para padding lateral */}
                <Routes>
                  <Route path="/dashboard" element={<DashboardPage />} />
                  <Route path="/vagas" element={<VagasPage />} />
                  <Route path="/veiculos" element={<VeiculosPage />} />
                  <Route path="/estacionamento" element={<EstacionamentoPage />} />
                  {/* Redirecionamento padrão para o dashboard se a rota for inválida */}
                  <Route path="*" element={<Navigate to="/dashboard" />} />
                </Routes>
              </div>
            </PrivateRoute>
          }
        />
      </Routes>
    </AuthProvider>
  );
}

export default App;
