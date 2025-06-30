const BASE_URL = "http://fix-api:8080";

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

    const res = await fetch(url);

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
