const BASE_URL = "http://localhost:9001/api";
// const BASE_URL = "/api";

// ============================================================ Récupération des tickets

export async function fetchTickets(endpointSuffix = "") {
  try {
    // Récupère la valeur 'projectId' depuis le localStorage
    // localStorage.getItem retourne soit une string, soit null si la clé n'existe pas
    const projectIdStr: string | null = localStorage.getItem("projectId");
    let projectIdNum: number;

    // Si projectId n'existe pas dans le localStorage
    if (!projectIdStr) {
      console.log(
        "Aucun projectId trouvé dans localStorage, valeur par défaut 1 utilisée."
      );

      // On définit la valeur par défaut à 1 (ça évite les problèmes)
      projectIdNum = 1;

      localStorage.setItem("projectId", projectIdNum.toString());
    } else {
      projectIdNum = parseInt(projectIdStr, 10);

      if (isNaN(projectIdNum) || projectIdNum <= 0) {
        throw new Error("Identifiant de projet invalide dans localStorage");
      }
    }

    // Nettoyage du suffixe d'URL pour éviter les doubles slashs
    // Si endpointSuffix commence par '/', on le retire
    const suffix = endpointSuffix.startsWith("/")
      ? endpointSuffix.slice(1)
      : endpointSuffix;

    // Construction finale de l'URL complète : base + /projets/{id}/ + suffixe
    // On supprime un éventuel slash final pour éviter les erreurs d'URL
    const url = `${BASE_URL}/projets/${projectIdNum}/${suffix}`.replace(
      /\/+$/,
      ""
    );

    // Récupération du token d'authentification depuis le localStorage
    const token = localStorage.getItem("authToken");

    const res = await fetch(url, {
      headers: {
        // Ajout du header Authorization avec le Bearer token
        Authorization: `Bearer ${token ?? ""}`,
      },
    });

    // Si la réponse HTTP n'est pas OK (200), on déclenche une erreur
    if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`);

    const data = await res.json();

    // On retourne soit la propriété Tickets (si existante), soit la totalité des données
    return data.Tickets || data;
  } catch (error) {
    console.error("Erreur fetchTickets:", error);
    throw error;
  }
}

// ============================================================ Création d'un nouveau ticket

export async function createTicket(ticketData) {
  // Récupérer projectId depuis localStorage ou valeur par défaut
  const projectIdStr = localStorage.getItem("projectId");
  let projectIdNum = 1;
  if (projectIdStr) {
    const parsed = parseInt(projectIdStr, 10);
    if (!isNaN(parsed) && parsed > 0) projectIdNum = parsed;
  } else {
    localStorage.setItem("projectId", "1");
  }

  const url = `${BASE_URL}/projets/${projectIdNum}/tickets`;

  // Récupération du token d'authentification depuis le localStorage
  // const token = localStorage.getItem("authToken");
  const token =
    "SKRZ0NBLTT0ZZZYY443A-JEU992C5Z7G3GG7RW5PR-FQ8MXG4EXJ17UAP5O3J5";

  const response = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      // Ajout du header Authorization avec le Bearer token
      Authorization: `Bearer ${token ?? ""}`,
    },
    body: JSON.stringify(ticketData),
  });

  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`);
  }

  const data = await response.json();
  return data;
}

export async function updateTicket(ticketId: number, ticketData: any) {
  const projectIdStr = localStorage.getItem("projectId");
  let projectIdNum = 1;
  if (projectIdStr) {
    const parsed = parseInt(projectIdStr, 10);
    if (!isNaN(parsed) && parsed > 0) projectIdNum = parsed;
  }

  const url = `${BASE_URL}/projets/${projectIdNum}/tickets/${ticketId}`;
  const token = localStorage.getItem("authToken") ?? "";

  const response = await fetch(url, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(ticketData),
  });

  if (!response.ok) {
    throw new Error(`Erreur HTTP ${response.status}`);
  }

  return await response.json();
}
