import React, { useEffect, useState } from "react";

type Props = {
  statut: string;
};

const DumpNombreTicket: React.FC<Props> = ({ statut }) => {
  const [count, setCount] = useState<number | null>(null);

  useEffect(() => {
    fetch("/data/tickets.json")
      .then((res) => {
        // console.log("Réponse HTTP:", res);
        if (!res.ok) throw new Error("Erreur HTTP " + res.status);
        return res.json();
      })
      .then((data) => {
        // console.log("Données chargées :", data);

        const countByStatus = data.filter(
          (ticket: { status: string }) => ticket.status === statut
        ).length;

        // console.log(
        //   `Nombre de tickets avec le statut "${statut}":`,
        //   countByStatus
        // );

        setCount(countByStatus);
      })
      .catch((err) => {
        // console.error("Erreur fetch :", err);
        setCount(null);
      });
  }, [statut]);

  return <div className="nb-DumpNombreTicket">{count !== null ? count : "Chargement..."}</div>;
};

export default DumpNombreTicket;
