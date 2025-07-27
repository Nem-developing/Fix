// --> apiService.ts - Appels API métier
//
// - Fonctions courtes et lisibles grâce aux utilitaires
// - Organisation par domaine (tickets, commentaires, graphiques)
// - Élimination des répétitions de code
// - Gestion d'erreur simplifiée

import {
  buildProjectUrl,
  apiGet,
  apiPost,
  apiPut,
  apiDelete,
} from "./apiHelpers";

// Types
type GraphEntry = {
  date: string;
  new: number;
  open: number;
  closed: number;
};

// ============================================================ Gestion des tickets

export async function fetchTickets(endpointSuffix = "") {
  try {
    const url = buildProjectUrl(endpointSuffix);
    const data = await apiGet(url);

    // On retourne soit la propriété Tickets (si existante), soit la totalité des données
    return data.Tickets || data;
  } catch (error) {
    console.error("Erreur fetchTickets:", error);
    throw error;
  }
}

export async function fetchTicketById(ticketId: number) {
  const url = buildProjectUrl(`tickets/${ticketId}`);
  return await apiGet(url);
}

export async function createTicket(ticketData: any) {
  const url = buildProjectUrl("tickets");
  return await apiPost(url, ticketData);
}

export async function updateTicket(ticketId: number, ticketData: any) {
  const url = buildProjectUrl(`tickets/${ticketId}`);
  return await apiPut(url, ticketData);
}

export async function fetchTicketsV2() {
  const url = buildProjectUrl("stats");
  return await apiGet(url);
}

// ============================================================ Gestion des commentaires

export async function fetchComments(ticketId: number) {
  const url = buildProjectUrl(`tickets/${ticketId}/commentaires`);
  return await apiGet(url);
}

export async function createComment(ticketId: number, commentData: any) {
  const url = buildProjectUrl(`tickets/${ticketId}/commentaires`);
  return await apiPost(url, commentData);
}

export async function updateComment(
  ticketId: number,
  commentId: number,
  commentData: any
) {
  const url = buildProjectUrl(`tickets/${ticketId}/commentaires/${commentId}`);
  return await apiPut(url, commentData);
}

export async function deleteComment(ticketId: number, commentId: number) {
  const url = buildProjectUrl(`tickets/${ticketId}/commentaires/${commentId}`);
  return await apiDelete(url);
}

// ============================================================ Données graphiques

export async function fetchGraphData(): Promise<Record<string, GraphEntry>> {
  const url = buildProjectUrl("graph");
  return await apiGet(url);
}
