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
  const [tickets, setTickets] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  useEffect(() => {
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
      })
      .catch((err) => {
        setError(err);
        setLoading(false);
      });
  }, [statut, format, data, exclude, result]);

  if (loading) return <p>Chargement...</p>;
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
