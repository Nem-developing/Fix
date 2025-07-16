import React, { useEffect, useState } from "react";
import { fetchTickets } from "../services/apiService";

interface DumpNombreTicketProps {
  statut?: string; // Filtrage par statut (ex: "open", "closed")
  format?: "table"; // Affichage sous forme de tableau si présent
  data?: string | string[]; // Colonnes à afficher
  exclude?: string | string[]; // Colonnes à exclure
  result?: "light"; // Mode "light" pour retourner juste un nombre
  extraWarningColumn?: boolean; // Ajoute une colonne "avertissement" si activé
  onTicketClick?: (ticket: any) => void; // Permet l'ouverture d'une popup de modification
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
  extraWarningColumn,
  onTicketClick,
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

    // Ajoute dynamiquement la colonne "avertissement" si activé
    const finalColumns = extraWarningColumn ? [...columns, "il y a"] : columns;

    return (
      <table className="DumpNombreTicket">
        <thead>
          <tr>
            {finalColumns.map((key) => (
              <th key={key}>{key}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {tickets.map((ticket, i) => {
            let daysLate: number | null = null;
            let isLate = false;

            if (extraWarningColumn && ticket.date) {
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
                  daysLate = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                  // Nombre de jours pour l'arvertissement
                  isLate = daysLate >= 7;
                }
              }
            }

            return (
              <tr
                key={i}
                onClick={() => onTicketClick?.(ticket)}
                className={onTicketClick ? "clickable-row" : ""}
              >
                {columns.map((key) => (
                  <td key={key}>{String(ticket[key])}</td>
                ))}
                {extraWarningColumn && (
                  <td key="avertissement">
                    {daysLate !== null
                      ? `${daysLate} jours${isLate ? " ❗" : ""}`
                      : "-"}
                  </td>
                )}
              </tr>
            );
          })}
        </tbody>
      </table>
    );
  }
};

export default DumpNombreTicket;
