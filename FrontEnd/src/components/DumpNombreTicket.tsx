import React, { useEffect, useState } from "react";
import { fetchTickets } from "../services/apiService";

interface DumpNombreTicketProps {
  statut?: string; // Filtrage par statut (ex: "open", "closed")
  format?: "table"; // Affichage sous forme de tableau si présent
  data?: string | string[]; // Colonnes à afficher
  exclude?: string | string[]; // Colonnes à exclure
  result?: "light"; // Mode "light" pour retourner juste un nombre
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

  // Mode "light" : on retourne uniquement le nombre de tickets filtrés
  if (result === "light") {
    return <div className="nb-DumpNombreTicket">{tickets.length}</div>;
  }

  // Affichage sous forme de tableau
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
      <table>
        <thead>
          <tr>
            {columns.map((key) => (
              <th key={key}>{key}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {tickets.map((ticket, i) => (
            <tr key={i}>
              {columns.map((key) => (
                <td key={key}>{String(ticket[key])}</td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    );
  }

  // Mode par défaut : affichage du nombre de tickets
  return <div className="nb-DumpNombreTicket">{tickets.length}</div>;
};

export default DumpNombreTicket;
