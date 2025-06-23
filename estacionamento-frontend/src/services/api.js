import axios from "axios";

const API_URL = "http://localhost:8000/api"; // Altere se necessário

export const api = axios.create({
  baseURL: API_URL,
});

// Interceptor para adicionar o token automaticamente
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});