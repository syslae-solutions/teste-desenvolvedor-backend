import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { api } from "../services/api";
import { useAuth } from "../context/AuthContext";

export default function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();

    // --- CÓDIGO DE BYPASS TEMPORÁRIO PARA TESTE (REMOVER EM PRODUÇÃO) ---
    if (email === "" && password === "") {
      login("dummy-token-for-initial-test"); // Define um token fictício
      navigate("/dashboard");
      return;
    }
    // --- FIM DO CÓDIGO DE BYPASS TEMPORÁRIO ---

    try {
      const response = await api.post("/login", { email, password });
      login(response.data.token);
      navigate("/dashboard");
    } catch (error) {
      alert("Erro no login. Verifique suas credenciais.");
      console.error("Detalhes do erro de login:", error.response || error); // Para depuração
    }
  };

  return (
    <div className="p-4 max-w-sm mx-auto">
      <h1 className="text-xl mb-4">Login</h1>
      <form onSubmit={handleSubmit}>
        <input
          type="email"
          placeholder="Email"
          className="block w-full mb-2 border p-2 rounded"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
        />
        <input
          type="password"
          placeholder="Senha"
          className="block w-full mb-4 border p-2 rounded"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />
        <button className="bg-blue-600 text-white px-4 py-2 rounded">
          Entrar
        </button>
      </form>
    </div>
  );
}