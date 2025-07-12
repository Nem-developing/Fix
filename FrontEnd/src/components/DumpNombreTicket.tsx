import React, { useEffect, useState } from "react";
import { fetchTickets } from "../services/apiService";

interface DumpNombreTicketProps {
  statut?: string; // Filtrage par statut (ex: "open", "closed")
  format?: "table"; // Affichage sous forme de tableau si présent
  data?: string | string[]; // Colonnes à afficher
  exclude?: string | string[]; // Colonnes à exclure
  result?: "light"; // Mode "light" pour retourner juste un nombre
  highlightLate?: boolean; // Active l'effet rouge clignotant pour les tickets vieux de plus de x jours
}

const statutMapping: Record<string, number> = {
  open: 0,
  inprogress: 1,
  blocked: 2,
  test: 3,
  review: 4,
  closed: 5,
};

const DumpNombreTicket: React.FC<DumpNombreTicketProps> = ({
  statut,
  format,
  data,
  exclude,
  result,
  highlightLate,
}) => {
  // Clé locale unique selon le filtre statut, format, etc
  const cacheKey = `tickets-cache-${statut ?? "all"}-${format ?? "count"}`;
  const initialCache = (() => {
    try {
      const cached = localStorage.getItem(cacheKey);
      return cached ? JSON.parse(cached) : [];
    } catch {
      return [];
    }
  })();

  const [tickets, setTickets] = useState<any[]>(initialCache);
  const [loading, setLoading] = useState(initialCache.length === 0); // charge uniquement si pas de cache
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
    setLoading(true);
    setError(null);

    // 1. Lire depuis localStorage (stale)
    const cached = localStorage.getItem(cacheKey);
    if (cached) {
      try {
        const cachedData = JSON.parse(cached);
        setTickets(cachedData);
        setLoading(false);
      } catch {
        // Ignorer erreur JSON parse
      }
    }

    // 2. Requête API (revalidate)
    fetchTickets("tickets")
      .then((data) => {
        let filtered = data;
        if (statut) {
          const statutNum = statutMapping[statut];
          if (statutNum !== undefined) {
            filtered = data.filter(
              (ticket: any) => ticket.statut === statutNum
            );
          }
        }

        setTickets(filtered);
        setLoading(false);

        // Mettre à jour le cache
        localStorage.setItem(cacheKey, JSON.stringify(filtered));
      })
      .catch((err) => {
        setError(err);
        setLoading(false);
      });
  }, [statut, format, data, exclude, result]);

  if (loading && tickets.length === 0) return <p>Chargement...</p>;
  if (error) return <p>Erreur: {error.message}</p>;

  if (result === "light") {
    return <div className="nb-DumpNombreTicket">{tickets.length}</div>;
  }

  if (format === "table") {
    const sample = tickets[0] || {};
    let columns = Object.keys(sample);

    if (data) {
      if (typeof data === "string") {
        columns = data.split(",").map((s) => s.trim());
      } else {
        columns = data;
      }
    } else if (exclude) {
      const excludeList = Array.isArray(exclude) ? exclude : [exclude];
      columns = columns.filter((col) => !excludeList.includes(col));
    }

    return (
      <table className="DumpNombreTicket">
        <thead>
          <tr>
            {columns.map((key) => (
              <th key={key}>{key}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {tickets.map((ticket, i) => {
            // Calculer si le ticket est "late" (date > x jours)
            let isLate = false;
            if (highlightLate && ticket.date) {
              const parts = ticket.date.split("/");
              if (parts.length === 3) {
                // Format DD/MM/YYYY -> YYYY-MM-DD
                const isoDate = `${parts[2]}-${parts[1].padStart(
                  2,
                  "0"
                )}-${parts[0].padStart(2, "0")}`;
                const ticketDate = new Date(isoDate);
                if (!isNaN(ticketDate.getTime())) {
                  const now = new Date();
                  const diffTime = now.getTime() - ticketDate.getTime();
                  const diffDays = diffTime / (1000 * 60 * 60 * 24);
                  isLate = diffDays > 3; // Nombre de jours pour le clignotement
                }
              }
            }

            return (
              <tr key={i} className={isLate ? "ticket-late" : ""}>
                {columns.map((key) => (
                  <td key={key}>{String(ticket[key])}</td>
                ))}
              </tr>
            );
          })}
        </tbody>
      </table>
    );
  }

  return <div className="nb-DumpNombreTicket">{tickets.length}</div>;
};

export default DumpNombreTicket;
