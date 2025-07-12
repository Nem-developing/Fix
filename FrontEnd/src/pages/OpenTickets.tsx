import React from "react";
import DumpNombreTicket from "../components/DumpNombreTicket";

const OpenTickets = () => {
  return (
    <div className="OpenTickets">
      <h1>Tickets Ouverts</h1>
      <DumpNombreTicket
        format="table"
        statut="open"
        data="id,titre,categorie,date"
        highlightLate={true}
      />
    </div>
  );
};

export default OpenTickets;
