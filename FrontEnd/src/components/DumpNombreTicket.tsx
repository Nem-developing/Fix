import React, { useEffect, useState } from "react";
import { fetchTickets } from "../services/apiService";

type Props = {
  statut: string;
};

const statutMapping: Record<string, number> = {
  open: 0,
  inprogress: 1,
  blocked: 2,
  test: 3,
  review: 4,
  closed: 5,
};

const DumpNombreTicket: React.FC<Props> = ({ statut }) => {
  const [count, setCount] = useState<number | null>(null);

  useEffect(() => {
    console.log("Appel fetchTickets avec statut =", statut);

    fetchTickets("tickets")
      .then((tickets) => {
        console.log("Tickets reçus :", tickets);

        const statutNum = statutMapping[statut];
        console.log("statutNum (nombre) correspondant :", statutNum);

        if (statutNum === undefined) {
          console.warn(`Statut inconnu: ${statut}`);
          setCount(null);
          return;
        }

        const countByStatus = tickets.filter(
          (ticket: { statut: number }) => ticket.statut === statutNum
        ).length;

        console.log(
          `Nombre de tickets avec statut ${statut} (num ${statutNum}):`,
          countByStatus
        );

        setCount(countByStatus);
      })
      .catch((err) => {
        console.error("Erreur fetchTickets :", err);
        setCount(null);
      });
  }, [statut]);

  return (
    <div className="nb-DumpNombreTicket">
      {count !== null ? count : "Chargement..."}
    </div>
  );
};

export default DumpNombreTicket;
