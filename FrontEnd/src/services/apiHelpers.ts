// --> apiHelper.ts - Utilitaires communs

// - Gestion centralisée des configurations (BASE_URL, tokens, projectId)
// - Fonctions utilitaires pour récupérer le projectId et le token
// - Construction automatique des URLs avec nettoyage des slashs
// - Fonctions génériques pour tous les types d'appels HTTP (GET, POST, PUT, DELETE)
// - Initialisation automatique des valeurs par défaut

const BASE_URL = "http://localhost:9001";

// Initialisation des valeurs par défaut
const initializeDefaults = () => {
  if (!localStorage.getItem("authToken")) {
    localStorage.setItem(
      "authToken",
      "GDN4E9R29QIVES1ZV286-STCBBUVCGZIY04NO0LP3-YQ7WKYY7JNVL8YIPNSN3"
    );
  }

  if (!localStorage.getItem("projectId")) {
    localStorage.setItem("projectId", "1");
  }
};

// Récupération du projectId avec validation
export const getProjectId = (): number => {
  const projectIdStr = localStorage.getItem("projectId");

  if (!projectIdStr) {
    console.log(
      "Aucun projectId trouvé dans localStorage, valeur par défaut 1 utilisée."
    );
    localStorage.setItem("projectId", "1");
    return 1;
  }

  const projectIdNum = parseInt(projectIdStr, 10);

  if (isNaN(projectIdNum) || projectIdNum <= 0) {
    throw new Error("Identifiant de projet invalide dans localStorage");
  }

  return projectIdNum;
};

// Récupération du token d'authentification
export const getAuthToken = (): string => {
  return localStorage.getItem("authToken") ?? "";
};

// Construction des headers standards
export const getHeaders = (includeContentType = false): HeadersInit => {
  const headers: HeadersInit = {
    Authorization: `Bearer ${getAuthToken()}`,
  };

  if (includeContentType) {
    headers["Content-Type"] = "application/json";
  }

  return headers;
};

// Construction d'URL avec nettoyage automatique
export const buildUrl = (endpoint: string): string => {
  // Nettoyage du endpoint pour éviter les doubles slashs
  const cleanEndpoint = endpoint.startsWith("/") ? endpoint.slice(1) : endpoint;

  // Construction et nettoyage de l'URL finale
  return `${BASE_URL}/${cleanEndpoint}`.replace(/\/+$/, "");
};

// Construction d'URL spécifique aux projets
export const buildProjectUrl = (endpointSuffix = ""): string => {
  const projectId = getProjectId();
  const suffix = endpointSuffix.startsWith("/")
    ? endpointSuffix.slice(1)
    : endpointSuffix;

  return buildUrl(`projets/${projectId}/${suffix}`);
};

// Fonction générique pour les appels API GET
export const apiGet = async (url: string) => {
  const response = await fetch(url, {
    headers: getHeaders(),
  });

  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`);
  }

  return await response.json();
};

// Fonction générique pour les appels API POST
export const apiPost = async (url: string, data: any) => {
  const response = await fetch(url, {
    method: "POST",
    headers: getHeaders(true),
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`);
  }

  return await response.json();
};

// Fonction générique pour les appels API PUT
export const apiPut = async (url: string, data: any) => {
  const response = await fetch(url, {
    method: "PUT",
    headers: getHeaders(true),
    body: JSON.stringify(data),
  });

  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`);
  }

  return await response.json();
};

// Fonction générique pour les appels API DELETE
export const apiDelete = async (url: string) => {
  const response = await fetch(url, {
    method: "DELETE",
    headers: getHeaders(),
  });

  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`);
  }

  return true;
};

// Initialisation au chargement du module
initializeDefaults();
