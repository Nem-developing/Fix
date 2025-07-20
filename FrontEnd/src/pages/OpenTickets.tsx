import React, { useState } from "react";
import DumpNombreTicket from "../components/tickets/DumpNombreTicket";
import TicketEditModal from "../components/tickets/TicketEditModal";

const OpenTickets = () => {
  const [selectedTicket, setSelectedTicket] = useState<any | null>(null);

  return (
    <div className="OpenTickets">
      <h1>Tickets Ouverts</h1>
      <DumpNombreTicket
        format="table"
        statut="open"
        data="id,titre,categorie,date"
        extraWarningColumn={true}
        onTicketClick={(ticket) => setSelectedTicket(ticket)}
      />

      {selectedTicket && (
        <TicketEditModal
          ticket={selectedTicket}
          onClose={() => setSelectedTicket(null)}
          onSave={(updated) => console.log("Nouveau ticket :", updated)}
        />
      )}
    </div>
  );
};

export default OpenTickets;
