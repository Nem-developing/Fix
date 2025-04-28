// src/components/ProtectedRoute/ProtectedRoute.js
import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';

// *** IMPORTANT ***
// Remplacez cette fonction par votre logique réelle de vérification d'authentification.
// Elle doit retourner true si l'utilisateur est authentifié, false sinon.
// Cela peut impliquer de vérifier un token dans localStorage/sessionStorage,
// de vérifier un état global (Context API, Redux, Zustand), etc.
const checkAuth = () => {
  // Exemple : Vérifier si un token existe dans localStorage
  const token = localStorage.getItem('authToken'); // Adaptez 'authToken' au nom réel de votre clé
  return !!token; // Retourne true si le token existe et n'est pas vide, false sinon
};

const ProtectedRoute = ({ element }) => {
  const isAuthenticated = checkAuth();
  const location = useLocation(); // Pour potentiellement rediriger après le login

  if (!isAuthenticated) {
    // Si non authentifié, redirige vers /login
    // `replace` évite d'ajouter l'ancienne page à l'historique
    // `state={{ from: location }}` permet de rediriger l'utilisateur vers la page
    // qu'il essayait d'accéder après s'être connecté (optionnel mais bonne pratique)
    return <Navigate to="/login" replace state={{ from: location }} />;
  }

  // Si authentifié, rend le composant demandé (la page)
  return element;
};

export default ProtectedRoute;
